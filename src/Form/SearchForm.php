<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Helpers\SearchData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'Tous les sites',
            ])
            ->add('dateMin', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('dateMax', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('sortiesPassees', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties passées'
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties dont je suis l\'organisateur'
            ])
            ->add('isInscrit', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je suis inscrit'
            ])
            ->add('isNotInscrit', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je ne suis pas inscrit'
            ])
            ->add('orderBy', ChoiceType::class, [
                'choices' => [
                    'Du plus récent au plus ancien' => 'ASC',
                    'Du plus ancien au plus récent' => 'DESC'
                ],
                'required' => false,
                'placeholder' => 'Trier par',

            ])
            ->add('submit', SubmitType::class);


    }


    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);

    }


    public function getBlockPrefix()
    {
        return '';
    }


}