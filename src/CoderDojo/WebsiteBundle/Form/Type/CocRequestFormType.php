<?php

namespace CoderDojo\WebsiteBundle\Form\Type;

use CoderDojo\WebsiteBundle\Entity\Dojo;
use CoderDojo\WebsiteBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ContactFormType
 * @codeCoverageIgnore
 */
class CocRequestFormType extends AbstractType
{
    /**
     * @var User[]
     */
    private $dojos;

    /**
     * @var User
     */
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
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
            ->add('letters', TextType::class, array(
                'label' => 'Voorletters'
            ))
            ->add('name', TextType::class, array(
                'label' => 'Achternaam'
            ))
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Opmerkingen',
                'attr' => [
                    'placeholder' => 'Let op, dit wordt ook met de vrijwilliger gedeeld!'
                ]
            ])
            ->add('dojo', ChoiceType::class, array(
                'label' => 'Dojo',
                'choices' => $this->buildChoices(),
                'attr' => [
                    'class' => 'form-control'
                ]
            ));
    }

    protected function buildChoices()
    {
        $choices = [];

        /** @var Dojo $dojo */
        foreach ($this->user->getDojos() as $dojo) {
            $choices[ $dojo->getName() ] = $dojo->getId();
        }

        return $choices;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coc_request';
    }
}
