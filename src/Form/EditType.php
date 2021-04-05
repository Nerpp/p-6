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
            // Display as select, true for checkbox
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
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'task_item',
        ]);
    }
}

