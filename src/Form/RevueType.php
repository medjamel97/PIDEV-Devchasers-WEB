<?php

namespace App\Form;

use App\Entity\CandidatureOffre;
use App\Entity\Revue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RevueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nbEtoiles')
            ->add('objet')
            ->add('description', TextareaType::class)
            ->add('candidatureOffre', EntityType::class, [
                'class' => CandidatureOffre::class,
                'choice_label' => 'id',
                'multiple' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Revue::class,
        ]);
    }
}
