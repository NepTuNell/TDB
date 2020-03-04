<?php

namespace SioBundle\Form;

use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class SituationDetailsType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('competences', EntityType::class, [
                    'class' => 'AdminBundle:Competences',
                    'multiple' => true,
                    'expanded' => true
                ])
                ->add('description', CKEditorType::class, [
                    'config_name' => 'my_config',
                ])
                ->add('pictures', CollectionType::class, [
                        'entry_type'        => PictureType::class,
                        'prototype'			=> true,
                        'allow_add'			=> true,
                        'allow_delete'		=> true,
                        'by_reference' 		=> false,
                        'required'			=> false,
                        'label'			    => false,
                ]);           
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SioBundle\Entity\SituationDetails',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'situationdetails_item',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'siobundle_situationdetails';
    }


}
