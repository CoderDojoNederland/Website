<?php

namespace CoderDojo\AdminBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('slug')
            ->add('body', TextareaType::class)
            ->add('image', FileType::class, ['required' => false, 'mapped'=>false])
            ->add('publishedAt', DateType::class, [
                'attr' => [
                    'class'=>'date-picker form-control'
                ],
                'required'=> false,
                'widget' => 'single_text'
            ])
            ->add('category', EntityType::class, [
                'class' => 'CoderDojo\WebsiteBundle\Entity\Category',
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'coderdojo_websitebundle_article';
    }
}
