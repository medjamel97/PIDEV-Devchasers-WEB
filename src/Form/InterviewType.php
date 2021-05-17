<?php

namespace App\Form;

use App\Entity\CandidatureOffre;
use App\Entity\Interview;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InterviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('difficulte', ChoiceType::class, [
                'choices' => [
                    'Très facile' => '0',
                    'Facile' => '1',
                    'Moyen' => '2',
                    'Difficile' => '3',
                    'Très difficile' => '4'
                ]])
            ->add('description', TextareaType::class)
            ->add('candidatureOffre', EntityType::class, [
                'class' => CandidatureOffre::class,
                'choice_label' => 'id',
                'multiple' => false]);;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Interview::class,
        ]);
    }
}
