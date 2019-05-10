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
class NewsletterType extends AbstractType
{
    /**
     * Build the form
     *
     * @param FormBuilderInterface $builder form builder
     * @param array                $options form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('FNAME', TextType::class, [
                'required' => true,
                'label' => 'Voornaam',
                'attr' => [
                    'id' => 'mce-FNAME'
                ]
            ])
            ->add('EMAIL', TextType::class, [
                'required' => true,
                'label' => 'Email',
                'attr' => [
                    'id' => 'mce-EMAIL'
                ]
            ])
            ->add('8354', ChoiceType::class, [
                'required' => true,
                'label' => 'Welke emails wil je ontvangen?',
                'choices' => [
                    'Ninja / Ouder' => 1,
                    'Champion / Mentor / Vrijwilliger' => 2
                ],
                'attr' => [
                    'id' => 'mce-EMAIL'
                ]
            ])
        ;
    }
}
