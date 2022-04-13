<?php

namespace App\Form;

use App\Entity\Comptabilite;
use App\Entity\TypeComptabilite;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComptabiliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle')
            ->add('description')
            ->add('date')
            ->add('id_type', EntityType::class, [
                // looks for choices from this entity
                'class' => TypeComptabilite::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'type',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comptabilite::class,
        ]);
    }
}
