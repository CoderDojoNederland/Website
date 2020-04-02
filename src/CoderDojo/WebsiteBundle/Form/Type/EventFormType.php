<?php

namespace CoderDojo\WebsiteBundle\Form\Type;

use CoderDojo\WebsiteBundle\Entity\DojoEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ProfileFormType
 * @codeCoverageIgnore
 */
class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['label'=>'Titel']);
        $builder->add('date', DateType::class, ['label'=>'Datum', 'widget'=>'single_text']);
        $builder->add('url', UrlType::class, ['label'=>'Registratie URL']);

        $attr = [];

        if ($builder->getData()->getEventType() === DojoEvent::TYPE_ONLINE) {
            $attr['checked'] = 'checked';
        }

        $builder->add(
            'online',
            CheckboxType::class,
            [
                'label' => 'Online Dojo',
                'required' => false,
                'mapped' => false,
                'attr' => $attr
            ]
        );
    }
}
