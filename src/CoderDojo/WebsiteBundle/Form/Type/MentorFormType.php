<?php

namespace CoderDojo\WebsiteBundle\Form\Type;

use CoderDojo\WebsiteBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Class ContactFormType
 * @codeCoverageIgnore
 */
class MentorFormType extends AbstractType
{
    /**
     * @var User[]
     */
    private $dojos;

    public function __construct(Registry $doctrine)
    {
        $this->dojos = $doctrine->getRepository('CoderDojoWebsiteBundle:Dojo')->findBy(['country' => 'nl'],['name'=>'asc']);
    }

    /**
     * Build the form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('naam', TextType::class, array(
                'label' => 'Naam:',
                'attr' => array(
                    'pattern'     => '.{2,}'
                )
            ))
            ->add('email', EmailType::class, [
                'label' => 'Email:'
            ])
            ->add('dojo', ChoiceType::class, [
                'label' => 'Dojo:',
                'empty_data' => '- Bij welke dojo wil je aansluiten? -',
                'data' => null,
                'mapped' => false,
                'choices' => $this->buildChoices(),
                'group_by' => function($val, $key, $index) {
                    return 'Lokale Dojo\'s';
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    protected function buildChoices()
    {
        $choices = [];

        /** @var User $dojo */
        foreach ($this->dojos as $dojo) {
            $choices[ $dojo->getName() ] = $dojo->getId();
        }

        return $choices;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $collectionConstraint = new Collection(array(
            'naam' => array(
                new NotBlank(array('message' => 'Name should not be blank.')),
                new Length(array('min' => 2))
            ),
            'email' => array(
                new NotBlank(array('message' => 'Email should not be blank.')),
                new Email(array('message' => 'Invalid email address.'))
            )
        ));

        $resolver->setDefaults(array(
            'constraints' => $collectionConstraint
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mentor';
    }
}
