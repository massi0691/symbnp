<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rating', IntegerType::class, $this->getConfiguration('Note sur 5',
                'Veuillez indiquez une note de 0 a 5', [
                    'attr'=> [
                        'min' =>0,
                        'max'=> 5,
                        'step'=>1
                    ],
                ]))
            ->add('content', TextareaType::class, $this->getConfiguration('Votre avis / témoignage',
                'N\'hésitez pas a étre plus précis, cela aidera nos futurs voyageurs ! '))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
