<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'required' => false,
            ])
            ->add('keyword', TextType::class, [
                'required' => false,
                'label' => 'Le nom de la sortie contient'
            ])
            ->add('dateDebut', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de début'
            ])
            ->add('dateFin', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de fin'
            ])
            ->add('choiceValue', ChoiceType::class, [
                'choices' => [
                    'Sorties dont je suis l\'organisateur/trice' => 'organisateur',
                    'Sorties auxquelles je suis inscrit/e' => 'inscrit',
                    'Sorties auxquelles je ne suis pas inscrit/e' => 'nonInscrit'
                ],
                'required' => false,
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('sortiesPassees', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties passées'
            ]);
    }
}

?>