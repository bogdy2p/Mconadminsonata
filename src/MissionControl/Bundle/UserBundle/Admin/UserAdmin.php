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
        $collection->add('clone', $this->getRouterIdParameter().'/clone');
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

        $date = new \DateTime();
        $form
                ->tab('UserData')
                ->with('N User information:')
                ->add('username')
                ->add('email')
                ->add('password')
                ->add('enabled', null, array('required' => false))
                ->add('firstname')
                ->add('lastname')
                ->add('office')
                ->add('phone')
                ->add('title')
//                ->add('roles')
                ->end()
                ->end()
                ->tab('UserAccess')
                ->with('Configure this user\'s Access')
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
//                ->add('id')
                ->add('username')
        ;
    }

}
