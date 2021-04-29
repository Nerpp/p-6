<?php

namespace App\Form;

use App\Entity\Images;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('trick')
        ->add('user')
        ->add(
            'image',
            FileType::class,
            [
                'label' => 'Votre image',

                'required' => false,
                'constraints' => [
                    new File(
                        [
                            'maxSize' => '1024k',
                            'mimeTypes' => [
                                "image/png",
                                "image/jpeg",
                                "image/jpg",
                                "image/gif",
                            ],
                            'mimeTypesMessage' => 'Veuillez télécharger un fichier conforme',
                        ]
                    )
                ]
            ]
        )
        
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Images::class,
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
