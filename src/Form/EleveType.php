<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\Note;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['placeholder' => 'Entrez le nom']
            ])
            ->add('prenom', TextType::class, [
                'attr' => ['placeholder' => 'Entrez le prénom']
            ])
            ->add('age', NumberType::class, [
                'attr' => ['min' => 0]
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Masculin' => 'M',
                    'Féminin' => 'F'
                ]
            ])
            ->add('classe', ChoiceType::class, [
                'choices' => [
                    'Classe 1' => 'C1',
                    'Classe 2' => 'C2'
                ]
            ])
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Photo de profil'
            ])
            ->add('notes', CollectionType::class, [
                'entry_type' => NoteType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'attr' => [
                    'class' => 'notes-collection'
                ]
            ])
          /* ->add('notes', NoteType::class, [
                'label' => false,


                'entry_type' => NumberType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                //'prototype' => true,
                'attr' => [
                    'class' => 'notes-collection'
                ]
            ])*/
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}