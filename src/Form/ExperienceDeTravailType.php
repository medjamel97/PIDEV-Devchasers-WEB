<?php

namespace App\Form;

use App\Entity\Candidat;
use App\Entity\ExperienceDeTravail;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExperienceDeTravailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('titreEmploi')
            ->add('nomEntreprise')
            ->add('ville')
            ->add('duree')
            ->add('candidat', EntityType::class, [
                'class' => Candidat::class,
                'choice_label' => 'id',
                'multiple' => false]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExperienceDeTravail::class,
        ]);
    }
}
