<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Seleccione una opinión'=> null,
                    'Opinion' => 'Opinion',
                    'Critica' => 'Critica',
                    'Humor' => 'Humor',
                    'Programación' => 'Programación',
                    'Aporte' => 'Aporte',
                    'Debate' => 'Debate'
                ],
            ])
            ->add('description')
            ->add('file',FileType::class, [
                'label'=>'Seleccione un archivo',
                'required'=>false
                
            ])
        //     ->add('creation_date', DateType::class, [
        //         // renders it as a single text box
        //         'widget' => 'single_text',
        //     ]
        // )
        //     ->add('url')
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
