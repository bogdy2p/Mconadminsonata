<?php

namespace MissionControl\Bundle\CampaignBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BrandType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('division')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MissionControl\Bundle\CampaignBundle\Entity\Brand'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'missioncontrol_bundle_campaignbundle_brand';
    }
}
