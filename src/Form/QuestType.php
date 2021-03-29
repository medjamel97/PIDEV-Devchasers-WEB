<?php

namespace App\Form;

use App\Entity\Questionnaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Mission;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class QuestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Mission', EntityType::class, [
                'class' => Mission::class,
                'choice_label' => 'mission_name',
                'multiple' => false])
            ->add('description')    
            ->add('Ajouter',SubmitType::class, array('label' => 'Ajouter >'));
            // ->add('question')
        ;

        // if($builder->get('sport')->isSubmitted()&& $form->isValid())
        // {
        //     $form->add('description');
        // }
 
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
        //     // ... adding the name field if needed
        //     $product = $event->getData();
        //     $form = $event->getForm();
           
        //     $form->add('description');

        // });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Questionnaire::class,
        ]);
    }
}
