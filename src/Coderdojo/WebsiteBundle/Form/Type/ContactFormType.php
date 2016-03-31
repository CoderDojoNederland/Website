<?php

namespace Coderdojo\WebsiteBundle\Form\Type;

use Coderdojo\WebsiteBundle\Entity\Dojo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
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

    public function __construct($dojos)
    {
        $this->dojos = $dojos;
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
            ->add('naam', 'text', array(
                'attr' => array(
                    'pattern'     => '.{2,}'
                )
            ))
            ->add('email', 'email')
            ->add('ontvanger', 'choice', [
                'empty_value' => '- Kies een dojo -',
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
            ->add('subject', 'text', array(
                'label' => 'Onderwerp'
            ))
            ->add('message', 'textarea', array(
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

        $choices['contact@coderdojo.nl'] = 'CoderDojo Nederland';

        /** @var Dojo $dojo */
        foreach ($this->dojos as $dojo) {
            $choices[ $dojo->getEmail() ] = $dojo->getName();
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