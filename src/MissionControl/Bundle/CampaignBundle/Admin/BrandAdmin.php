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

    public function configureListFields(ListMapper $list) {

        $list
                ->addIdentifier('id')
                ->add('name')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                    )
                ))
        ;
    }

    public function configureFormFields(FormMapper $form) {


        $form
                ->with('General')
                ->add('name')
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
        ;
    }

}