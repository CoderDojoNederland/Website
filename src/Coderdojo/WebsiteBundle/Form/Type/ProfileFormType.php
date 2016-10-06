<?php

namespace Coderdojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProfileFormType
 * @codeCoverageIgnore
 */
class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('email');
        $builder->remove('username');
        $builder->remove('current_password');
        $builder->add('name');
        $builder->add('location');
        $builder->add('street');
        $builder->add('housenumber');
        $builder->add('postalcode');
        $builder->add('city');
        $builder->add('facebook');
        $builder->add('twitter');
        $builder->add('website');
        $builder->add('organiser');
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
