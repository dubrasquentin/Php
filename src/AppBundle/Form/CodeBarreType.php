<?php

// src/Form/TaskType.php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CodeBarreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('code_barre', TextType::class, [
            'attr' => [
                'placeholder' => 'Code barre EAN 13 chiffres'
            ]
        ])
        ;
    }
}
