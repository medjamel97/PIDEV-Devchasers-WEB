<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Societe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mission_name')
            ->add('date')
            ->add('nbheure')
            ->add('prixH')
            ->add('description')
            ->add('societe', EntityType::class, [
                'class' => Societe::class,
                'choice_label' => 'nomSociete',
                'multiple' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
