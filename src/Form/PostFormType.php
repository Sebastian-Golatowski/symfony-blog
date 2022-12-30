<?php

namespace App\Form;

use App\Entity\Post;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PostFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = ['required'=>true]): void
    {
        $builder
            ->add('title', TextType::class,[
                'attr' => array(
                    'placeholder' => 'Enter title...',
                    'class'=>''
                ),
                'label' => false,
                'required'=>false,
            ])
            ->add('text',TextareaType::class, [
                'attr' => array(
                    'placeholder' => 'Enter Content...'
                ),
                'label' => false,
                'required'=>false,
            ])
            ->add('img', FileType::class, [
                'label' => false,
                'required'=>$options['required'],
                'mapped'=> false
            ])
            // ->add('user',HiddenType::class,[
            //     'value' =>
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
            'required' => true,
        ]);

        $resolver->setAllowedTypes('required','bool');
    }
}
