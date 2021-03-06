<?php

namespace MissionControl\Bundle\CampaignBundle\Admin;

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
class AccessAdmin extends Admin {

    public function createQuery($context = 'list') {

        $query = parent::createQuery($context);
        $query->andWhere(
                $query->expr()->neq($query->getRootAliases()[0] . '.client', ':value1')
        );
        $query->setParameter('value1', '2');  // 2 Is TEMP_CLIENT for THIS CASE
        return $query;
    }

    public function configureListFields(ListMapper $list) {


        $list
                ->addIdentifier('id')
                ->add('user')
                ->add('client', null, array('type' => 'text'))
                ->add('region')
                ->add('country')
                ->add('all_countries')
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

//        var_dump($form->getFormBuilder()->get('UserAdmin'));
//        die('I DIED');
        
        $form
                
                ->add('user')
                ->add('client')
                ->add('region')
                ->add('country')
                ->add('all_countries')
                ->end()
        ;
    }

    public function preUpdate($object) {
        foreach ($object->getUseraccesses() as $useraccess) {
            $useraccess->setUser($object);
        }
    }

    public function configureShowFields(ShowMapper $show) {

        $show
                ->add('user')
                ->add('client')
                ->add('region')
                ->add('country')
                ->add('all_countries')
        ;
    }

    public function configureDatagridFilters(DatagridMapper $filter) {
        $filter
                ->add('user')
                ->add('client')
        ;
    }

}
