<?php

namespace Coderdojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class RegistrationFormType
 * @codeCoverageIgnore
 */
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder->remove('username');
        $builder->add('name', null, array('label' => 'Dojo Name'));
        $builder->add('location', null, array('label' => 'Location Name'));
        $builder->add('street');
        $builder->add('housenumber');
        $builder->add('postalcode');
        $builder->add('city');
        $builder->add('facebook', null, array('attr'=> array('placeholder' => 'http://facebook.com/yourpage')));
        $builder->add('twitter', null, array('attr'=> array('placeholder' => 'http://twitter.com/yourtwitter')));
        $builder->add('website', null, array('attr'=> array('placeholder' => 'http://coderdojo-[CITY].nl')));
        $builder->add('organiser', null, array('attr'=> array('placeholder' => 'Eventbrite Organiser ID (see link below)')));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'coderdojo_user_registration';
    }
}
