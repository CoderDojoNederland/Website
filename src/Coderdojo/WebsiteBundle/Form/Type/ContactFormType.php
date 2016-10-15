<?php

namespace Coderdojo\WebsiteBundle\Form\Type;

use Coderdojo\WebsiteBundle\Entity\Dojo;
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
class ContactFormType extends AbstractType
{
    /**
     * @var Dojo[]
     */
    private $dojos;

    /**
     * @var Registry
     */
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->dojos = $this->doctrine->getRepository('CoderdojoWebsiteBundle:Dojo')->findBy([],['name'=>'asc']);
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
                'attr' => array(
                    'pattern'     => '.{2,}'
                )
            ))
            ->add('email', EmailType::class)
            ->add('ontvanger', ChoiceType::class, [
                'empty_data' => '- Kies een dojo -',
                'mapped' => false,
                'choices' => $this->buildChoices(),
                'group_by' => function($val, $key, $index) {
                    if ('contact@coderdojo.nl' !== $val) {
                        return 'Lokale Dojo\'s';
                    } else {
                        return 'Algemene zaken';
                    }
                },
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('subject', TextType::class, array(
                'label' => 'Onderwerp'
            ))
            ->add('message', TextareaType::class, array(
                'label' => 'Bericht',
                'attr' => array(
                    'cols' => 90,
                    'rows' => 10
                )
            ));
    }

    protected function buildChoices()
    {
        $choices = [];

        $choices['CoderDojo Nederland'] = 'contact@coderdojo.nl';

        /** @var Dojo $dojo */
        foreach ($this->dojos as $dojo) {
            $choices[ $dojo->getName() ] = $dojo->getEmail();
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
            ),
            'subject' => array(
                new NotBlank(array('message' => 'Subject should not be blank.'))
            ),
            'message' => array(
                new NotBlank(array('message' => 'Message should not be blank.')),
                new Length(array('min' => 5))
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
        return 'contact';
    }
}
