<?php

namespace AG\AgenceimmoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImmobilierType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('typeimmobilier', 'entity', array(
				'class' => 'AGAgenceimmoBundle:Typeimmobilier',
				'property' => 'denomination',
				'multiple' => false,
				'expanded' => false))
            ->add('denomination', 'textarea')
            ->add('surface')
            ->add('location','checkbox', array('required'=>false))
            ->add('nbPieces')
            ->add('annee')
            ->add('prix')
            ->add('images', 'collection', array(
				  'type'         => new ImageType(),
				  'allow_add'    => true,
				  'allow_delete' => true
				   ))
			->add('save','submit', array('attr' => array('class' => 'btn btn-primary'),))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AG\AgenceimmoBundle\Entity\Immobilier'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ag_agenceimmobundle_immobilier';
    }
}
