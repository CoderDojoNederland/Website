<?php

namespace CoderDojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @codeCoverageIgnore
 */
class ClubOf100FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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

        $builder->add('email', EmailType::class, array(
            'label' => 'form.email',
            'translation_domain' => 'FOSUserBundle',
            'attr' => [
                'placeholder' => 'some@email.com'
            ]
        ));

        $builder->add('reason', TextType::class, array(
            'label' => 'Rede',
            'attr' => [
                'placeholder' => 'Beschrijf kort waarom je lid wordt van onze club van 100.'
            ]
        ));

        $builder->add('public', ChoiceType::class, array(
            'label' => 'Deelname',
            'choices' => [
                'Publiek' => '1',
                'Anoniem' => '0'
            ],
            'attr' => [
                'class' => 'form-control'
            ],
            'expanded' => false,
            'multiple' => false
        ));

        $builder->add('subscription', ChoiceType::class, array(
            'label' => 'Betaling',
            'choices' => [
                'Jaarlijks €100 (1 juni)' => 'yearly',
                'Halfjaarlijks €50 (1 april & 1 oktober)' => 'semi-yearly',
                'Kwartaallijks €25 (1 januari, 1 april, 1 juli, 1 oktober)' => 'quarterly'
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
