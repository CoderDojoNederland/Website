<?php

namespace Coderdojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->remove('username');
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

    public function getName()
    {
        return 'coderdojo_user_registration';
    }
}
