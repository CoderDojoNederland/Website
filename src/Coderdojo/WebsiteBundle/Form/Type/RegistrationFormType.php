<?php

namespace Coderdojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

/**
 * Class RegistrationFormType
 * @codeCoverageIgnore
 */
class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

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

    public function getName()
    {
        return 'coderdojo_user_registration';
    }
}
