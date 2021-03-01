<?php

namespace App\Form;

use App\Entity\Revue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RevueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nbEtoiles')
            ->add('objet')
            ->add('description')
            ->add('candidatureOffre')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Revue::class,
        ]);
    }
}
