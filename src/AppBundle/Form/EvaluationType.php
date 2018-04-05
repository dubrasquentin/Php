<?php
/**
 * Created by PhpStorm.
 * User: Quentin
 * Date: 15/01/2018
 * Time: 17:40
 */

namespace AppBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use function Symfony\Component\Validator\Tests\Constraints\choice_callback;

class EvaluationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', ChoiceType::class, array('choices'=> array('1' => 1,'2' => 2,'3' => 3,'4' => 4,'5' => 5)))
            ->add("commentaire", TextareaType::class)
            ->add('Envoyer', SubmitType::class);
    }
}