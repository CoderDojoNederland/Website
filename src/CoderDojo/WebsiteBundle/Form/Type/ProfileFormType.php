<?php

namespace CoderDojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProfileFormType
 * @codeCoverageIgnore
 */
class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('plainPassword');
        $builder->remove('username');
        $builder->remove('email');
        $builder->add('firstName', null, ['label'=>'Voornaam']);
        $builder->add('lastName', null, ['label'=>'Achternaam']);
        $builder->add('email', EmailType::class, ['label'=>'Email']);
        $builder->add('phone', null, ['label'=>'Telefoon']);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'coderdojo_user_profile';
    }
}
