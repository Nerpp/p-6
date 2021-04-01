<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;




class EditType extends AbstractType
{

  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', EntityType::class, [
            // Look for choices from the Categories entity
            'class' => Trick::class,
            // Display as checkboxes
            'expanded' => false,
            'multiple' => false,
            // The property of the Categories entity that will show up on the select (or checkboxes)
            'choice_label' => 'name' 
        ])
        
        
        ;
    
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}

