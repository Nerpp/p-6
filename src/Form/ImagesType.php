<?php

namespace App\Form;

use App\Entity\Images;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\IsTrue;
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
        ->add('featured', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Selectionner l\'image de présentation',
                    ]),
                ],
            ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Images::class,
        ]);
    }
}
