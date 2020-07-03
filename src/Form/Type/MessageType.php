<?php

namespace App\Form\Type;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('priority', IntegerType::class);
        $builder->add('value');
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSet']);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Message::class,
                                'csrf_protection' => false,
                               ]);
    }
    
    public function preSet(FormEvent $event)
    {
        if ($message = $event->getData()) {
            $message->setId(hash("crc32b", hash('sha256', uniqid(mt_rand(), true), true)));
        }
    }
}
