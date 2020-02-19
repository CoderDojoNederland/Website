<?php

namespace CoderDojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ContactFormType
 * @codeCoverageIgnore
 */
class DonationFormType extends AbstractType
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
            ->add('comment', TextAreaType::class, array(
                'label' => 'Commentaar:',
                'attr' => [
                    'expanded' => true
                ]
            ))
            ->add('amount', HiddenType::class, [
                'required' => true
            ])
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'donation';
    }
}
