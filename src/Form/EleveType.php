<?php

namespace App\Form;

use App\Entity\Eleve;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('nom')
//            ->add('prenom')
//            ->add('age')
//            ->add('sexe')
//            ->add('classe')
//            ->add('avatar')
//            ->add('note')
//        ;
            ->add('nom')
            ->add('prenom')
            ->add('age')
            ->add('sexe', ChoiceType::class, [
                'choices' => ['M' => 'M', 'F' => 'F']
            ])
            ->add('classe', ChoiceType::class, [
                'choices' => ['C1' => 'C1', 'C2' => 'C2']
            ])
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'required' => false
            ])
            ->add('notes', CollectionType::class, [
                'entry_type' => NumberType::class,
                'allow_add' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
