<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\OffreDeTravail;
use App\Entity\Societe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffreDeTravailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'id',
                'multiple' => false])
            ->add('societe', EntityType::class, [
                'class' => Societe::class,
                'choice_label' => 'id',
                'multiple' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OffreDeTravail::class,
        ]);
    }
}
