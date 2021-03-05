<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\OffreDeTravail;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomCategorie')
            ->add('offreDeTravail', EntityType::class, [
                'class' => OffreDeTravail::class,
                'choice_label' => 'id',
                'multiple' => false,]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
