<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
                'required' => true
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
            ->add('organisateur', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties dont je suis l\'organisateur/trice'
            ])
            ->add('inscrit', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je suis inscrit/e'
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e'
            ])
            ->add('sortiesPassees', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties passées'
            ]);
    }
}

?>