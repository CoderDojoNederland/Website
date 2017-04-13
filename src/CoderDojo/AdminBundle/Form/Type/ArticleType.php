<?php

namespace CoderDojo\AdminBundle\Form\Type;

use CoderDojo\WebsiteBundle\Entity\Category;
use CoderDojo\WebsiteBundle\Repository\CategoryRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ArticleType extends AbstractType
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('slug')
            ->add('body', TextareaType::class)
            ->add('image', FileType::class, ['required' => false])
            ->add('publishedAt', null, ['attr'=>['class'=>'date-picker form-control'], 'required'=> false])
            ->add('category', ChoiceType::class, [
                'choices' => $this->categoryChoices(),
                'expanded' => false,
                'multiple' => false,
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
        return 'coderdojo_websitebundle_category';
    }

    private function categoryChoices()
    {
        $choices = [];

        /** @var Category[] $categories */
        $categories = $this->doctrine->getRepository(Category::class)->findAll();

        foreach($categories as $category) {
            $choices[$category->getTitle()] = $category->getUuid();
        }

        return $choices;
    }
}
