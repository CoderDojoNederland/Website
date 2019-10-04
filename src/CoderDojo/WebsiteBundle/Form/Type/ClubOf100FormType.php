<?php

namespace CoderDojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @codeCoverageIgnore
 */
class ClubOf100FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', null, [
            'label'       => 'Voornaam*',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        $builder->add('lastName', null, [
            'label' => 'Achternaam*',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        $builder->add('email', EmailType::class, array(
            'label' => 'Email*',
            'required' => true,
            'translation_domain' => 'FOSUserBundle',
            'attr' => [
                'placeholder' => 'some@email.com'
            ]
        ));

        $builder->add('reason', TextareaType::class, array(
            'label' => 'Reden*',
            'required' => true,
            'attr' => [
                'placeholder' => 'Beschrijf kort waarom je lid wordt van onze club van 100.'
            ]
        ));

        $builder->add('twitter', TextType::class, array(
            'label' => 'Twitter',
            'required' => false,
            'attr' => [
                'placeholder' => '@username'
            ]
        ));

        $builder->add('company', TextType::class, array(
            'label' => 'Bedrijf',
            'required' => false,
            'attr' => [
                'placeholder' => 'Doneer je vanuit een bedrijf? Dan weten we graag welk bedrijf.'
            ]
        ));

        $builder->add('type', ChoiceType::class, array(
            'label' => 'Lidtype*',
            'required' => true,
            'choices' => [
                'Geen onderdeel van de community' => 'none',
                'Vrijwilliger' => 'volunteer',
                'Ouder' => 'parent',
            ],
            'attr' => [
                'class' => 'form-control'
            ]
        ));

        $builder->add('avatar', FileType::class, array(
            'label' => 'Profielfoto',
            'required' => false,
            'constraints' => [
                new Image([
                    'minWidth' => 100,
                    'minHeight' => 100,
                    'maxHeight' => 1000,
                    'maxWidth' => 1000,
                    'maxSize' => 5242880 // 5mb
                ])
            ]
        ));

        $builder->add('public', ChoiceType::class, array(
            'label' => 'Deelname*',
            'required' => true,
            'choices' => [
                'Publiek' => '1',
                'Anoniem' => '0'
            ],
            'attr' => [
                'class' => 'form-control'
            ]
        ));

        $builder->add('subscription', ChoiceType::class, array(
            'label' => 'Betaling*',
            'required' => true,
            'choices' => [
                'Jaarlijks €100' => 'yearly',
                'Halfjaarlijks €50' => 'semi-yearly',
                'Kwartaallijks €25' => 'quarterly'
            ],
            'attr' => [
                'class' => 'form-control'
            ],
            'expanded' => true,
            'multiple' => false
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
}
