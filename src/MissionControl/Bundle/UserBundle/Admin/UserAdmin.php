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

       public function createQuery($context = 'list') {

        $query = parent::createQuery($context);
        $query->andWhere(
                $query->expr()->neq($query->getRootAliases()[0] . '.username', ':value1')
        );
        $query->setParameter('value1','qa_user');  // qa_user is quality-assurance user , do not show it.
        return $query;
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
                ->with('Please provide the new User information:')
                ->add('username')
                ->add('email')
                ->add('password')
                ->add('enabled', null, array('required' => false))
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
