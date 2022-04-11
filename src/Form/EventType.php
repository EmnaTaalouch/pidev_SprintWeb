<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_event',TextType::class)
            ->add('event_description',TextareaType::class)
            ->add('event_theme',TextType::class)
            ->add('date_debut',DateType::class,['attr' => ['class' => 'form-control']])
            ->add('date_fin',DateType::class,['attr' => ['class' => 'form-control']])
            ->add('event_status',ChoiceType::class, [
                'choices'  => [
                    'publique' => 'publique',
                    'privé' => 'privé',
                ]])
            ->add('nbr_participants',NumberType::class)
            ->add('lieu',TextType::class)
            ->add('image_event',FileType::class,[
                'attr' => ['class' => 'form-control'],
                'label' => 'Document',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid IMAGE document',
                    ])
                ],
            ])

            ->add('id_type',EntityType::class,
                [
                'class' => \App\Entity\EventType::class,
                'choice_label' => 'libelle',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
