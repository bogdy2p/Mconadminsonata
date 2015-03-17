<?php

namespace MissionControl\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Description of UserAdmin
 *
 * @author pbc
 */
class UserAdmin extends Admin {

    public function createQuery($context = 'list') {

        $query = parent::createQuery($context);
        $query->andWhere(
                $query->expr()->neq($query->getRootAliases()[0] . '.username', ':value1')
        );
        $query->setParameter('value1', 'qa_user');  // qa_user is quality-assurance user , do not show it.
        return $query;
    }

    public function configureRoutes(RouteCollection $collection) {
        $collection->add('clone', $this->getRouterIdParameter() . '/clone');
    }

    public function configureListFields(ListMapper $list) {

        $list
                ->addIdentifier('id')
                ->add('username')
                ->add('email')
                ->add('lastname')
                ->add('firstname')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'Clone' => array(
                            'template' => 'UserBundle:CRUD:list__action_clone.html.twig'
                        ),
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    )
                ))
        ;
    }

    public function configureFormFields(FormMapper $form) {

//        $form
//                ->with('User')
//                ->add('username')
//                ->add('email')
//                ->add('lastname')
//                ->add('firstname')
//                ->add('password')
//                ->add('roles')
//                ->add('phone')
//                ->add('title')
//                ->add('office')
//                ->end()
////                ->with('UserAccess')
////                ->add('accesses', 'sonata_type_collection', array(
////                    'required' => false,
////                        ), array(
////                    'edit' => 'inline',
////                    'inline' => 'table',
////                    'sortable' => 'position',
////                        ), array(
////                ))
////                ->end()
//        ;
        
        
          $form
                ->with('User')
                ->add('username', 'sonata_type_admin', array(
                    'class' => 'UserBundle:User',
                    'required' => false,
                        ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                ))
                ->end();
        $form->with('UserAccess')
                ->add('accesses', 'sonata_type_collection', array(
                    'required' => false,
                        ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                        ), array(
                ))
                ->end()

        ;
    }

    public function postPersist($user) {


//        $this->configureFormFields($user)
//                ->with('UserAccess')
//                 ->add('accesses', 'sonata_type_collection', array(
//                    'required' => false,
//                        ), array(
//                    'edit' => 'inline',
//                    'inline' => 'table',
//                    'sortable' => 'position',
//                        ), array(
//                ))
//                ->end()
//        ;


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
//                ->add('id')
                ->add('username')
        ;
    }

}
