<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends ApplicationType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration(
                'Titre',
                'Tapez un super titre pour votre annonce !'))
            ->add('slug', TextType::class, $this->getConfiguration(
                'Chaine URL',
                'Adresse wab (automatique)',[
                'required'=> false
            ]))
            ->add('coverImage', UrlType::class, $this->getConfiguration(
                "URL de l'image principale",
                "Donnez l'adresse d'une image qui donne vraiment envie"))
            ->add('introduction', TextType::class, $this->getConfiguration(
                'Introduction',
                'Donnez une description globale de l\'annonce'))
            ->add('content', TextareaType::class, $this->getConfiguration(
                'Description détaillée',
                'Tapez une description qui donne envie de venir chez vous !'))
            ->add('rooms', IntegerType::class, $this->getConfiguration(
                'Chambres',
                'Le nombre de chambre disponible '))
            ->add('price', MoneyType::class, $this->getConfiguration(
                'Prix',
                'Tapez un prix'))
            ->add('images', CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete'=>true,
                ]
            );

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
