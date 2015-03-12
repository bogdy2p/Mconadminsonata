<?php

namespace MissionControl\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Description of UserAdmin
 *
 * @author pbc
 */
class UserAdmin extends Admin {

    public function configureListFields(ListMapper $list) {

        $list
                ->addIdentifier('id')
                ->add('username')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                    )
                ))
        ;
    }

    public function configureFormFields(FormMapper $form) {

        $date = new \DateTime();
        $form
                ->with('Please provide the new User information:')
                ->add('username')
                ->add('email')
                ->add('password')
                ->add('enabled')
                ->add('firstname')
                ->add('lastname')
                ->add('office')
                ->add('phone')
                ->add('title')
                ->end()
                
                
        ;

    }

    public function configureShowFields(ShowMapper $show) {

        $show
                ->add('username')
                ->add('email')
                ->add('website')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $filter) {
        $filter
                ->add('id')
                ->add('username')
        ;
    }

}
