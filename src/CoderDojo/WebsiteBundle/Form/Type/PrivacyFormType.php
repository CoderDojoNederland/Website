<?php

namespace CoderDojo\WebsiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProfileFormType
 * @codeCoverageIgnore
 */
class PrivacyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('consent', CheckboxType::class, [
            'required' => true,
            'mapped' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return 'coderdojo_user_profile';
    }
}
