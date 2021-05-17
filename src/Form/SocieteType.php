<?php

namespace App\Form;

use App\Entity\Societe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocieteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('dateCreation', DateType::class, [
                'years' => range(date('Y') - 250, date('Y')),
            ])
            ->add('tel', IntegerType::class)
            ->add('adresse')
            ->add('idPhoto', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Societe::class,
        ]);
    }
}
