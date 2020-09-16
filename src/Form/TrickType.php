<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            //Gestion champ groupe
            ->add('groupe')
            //on ajoute le champ "images" dans le formulaire
            //il n'est pas lié a la bdd (mapped => false)
            ->add('images',FileType::class,[
                'label' => 'Selectionner votre image',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                ])
            ->add('videos', CollectionType::class, array(
                'entry_type'   => VideosType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'required' => false
            ))
        ;



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
