<?php

namespace Coderdojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

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
        $builder->add('name', null, [
            'label'       => 'Dojo Name',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('location', null, [
            'label' => 'Location Name',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('street', null, [
            'constraints' => [
                new NotBlank()
            ]
        ]);
        $builder->add('housenumber', null, [
            'constraints' => [
                new NotBlank()
            ]
        ]);
        $builder->add('postalcode', null, [
            'constraints' => [
                new NotBlank(),
                new Length(6)
            ]
        ]);
        $builder->add('city', null, [
            'constraints' => [
                new NotBlank()
            ]
        ]);
        $builder->add('facebook', null, [
            'attr' => [
                'placeholder' => 'https://facebook.com/yourpage'
            ],
            'constraints' => [
                new NotBlank(),
                new Url()
            ]
        ]);
        $builder->add('twitter', null, [
            'attr' => [
                'placeholder' => 'https://twitter.com/yourtwitter'
            ],
            'constraints' => [
                new NotBlank(),
                new Url()
            ]
        ]);
        $builder->add('website', null, [
            'attr' => [
                'placeholder' => 'http://www.yourwebsite.com'
            ],
            'constraints' => [
                new NotBlank(),
                new Url()
            ]
        ]);
        $builder->add('organiser', null, [
            'attr' => [
                'placeholder' => 'Eventbrite Organiser ID (see link below)',
            ],
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min'=>8,
                    'max'=>12,
                    'minMessage' => 'Dit is geen geldige ID. Zie onderstaande link voor meer uitleg',
                    'maxMessage' => 'Dit is geen geldige ID. Zie onderstaande link voor meer uitleg.'
                ])
            ]
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
