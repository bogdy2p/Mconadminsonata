<?php

namespace MissionControl\Bundle\CampaignBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Description of BrandAdmin
 *
 * @author pbc
 */
class BrandAdmin extends Admin {

    public function createQuery($context = 'list') {

        $query = parent::createQuery($context);
        $query->andWhere(
                $query->expr()->neq($query->getRootAliases()[0] . '.name', ':value1')
        );
        $query->setParameter('value1', 'temp_brand');  // Do not show temp_brand
        return $query;
    }

    public function configureListFields(ListMapper $list) {

        $list
                ->addIdentifier('id')
                ->add('name')
                ->add('division')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    )
                ))
        ;
    }

    public function configureFormFields(FormMapper $form) {


        $form
                ->with('Please provide the new Brand information:')
                ->add('name')
                ->add('division')
                ->end()
        ;
    }

    public function configureShowFields(ShowMapper $show) {

        $show
                ->add('name')
                ->add('email')
                ->add('website')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $filter) {
        $filter
                ->add('id')
                ->add('name')
                ->add('division')
        ;
    }

}
