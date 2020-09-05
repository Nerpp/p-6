<?php

namespace App\Form;


use App\Entity\Pictures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ImagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'image',
                FileType::class,
                [
                    'label' => 'Votre image',
                    // unmapped means that this field is not associated to any entity property
                    'mapped' => false,
                    // make it optional so you don't have to re-uploads the image
                    // every time you edit the Product details
                    'required' => false,
                    // unmapped fields can't define their validation using annotations
                    // in the associated entity, so you can use the PHP constraint classes
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

//    lien vers Doctrine l'entité ou la collection sera enregistré
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pictures::class,
        ]);
    }
}
