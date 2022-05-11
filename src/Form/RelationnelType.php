<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Relationnel;
use blackknight467\StarRatingBundle\Form\RatingType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationnelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('image',FileType::class, [
                'label'=>false,
                'mapped'=>false,
                'required'=>false
            ])
            ->add('categorie', EntityType::class, [
                // looks for choices from this entity
                'class' => Categorie::class,
                'choice_label' => 'role',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Relationnel::class,
        ]);
    }
}
