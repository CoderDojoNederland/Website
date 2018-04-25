<?php

namespace CoderDojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RegistrationFormType
 * @codeCoverageIgnore
 */
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->remove('username');
        $builder->remove('email');
        $builder->remove('plainPassword');

        $builder->add('firstName', null, [
            'label'       => 'Voornaam',
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        $builder->add('lastName', null, [
            'label' => 'Achternaam',
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        $builder->add('phone', null, [
            'label' => 'Telefoon',
            'attr' => [
                'placeholder' => 'Wordt enkel gebruikt voor dringende zaken over jouw dojo(s)'
            ],
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => 10,
                    'max' => 10
                ])
            ]
        ]);

        $builder->add('email', EmailType::class, array(
            'label' => 'form.email',
            'translation_domain' => 'FOSUserBundle',
            'attr' => [
                'placeholder' => 'Gebruik aub een persoonlijk emailadres'
            ]
        ));

        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'options' => array('translation_domain' => 'FOSUserBundle'),
            'first_options' => array('label' => 'form.password'),
            'second_options' => array('label' => 'form.password_confirmation'),
            'invalid_message' => 'fos_user.password.mismatch',
        ));

        $builder->add('consent', CheckboxType::class, [
            'label' => 'Ik ga akkoord met de verwerking van mijn gegevens volgens de Privacy Verklaring.',
            'label_attr' => [
                'class' => 'col-lg-9 checkbox-label'
            ],
            'required' => true,
            'mapped' => false
        ]);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'coderdojo_user_registration';
    }
}
