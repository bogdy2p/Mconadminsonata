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
class UseraccessAdmin extends Admin {

    public function createQuery($context = 'list') {

        $query = parent::createQuery($context);
//        $query->andWhere(
//                $query->expr()->eq($query->getRootAliases()[0] . '.client', ':my_param')
//        );
//        $query->setParameter('client','all_clients');
        
        
        return $query;
    }

    public function configureListFields(ListMapper $list) {




        $list
                ->addIdentifier('id')
                ->add('user')
                ->add('client')
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

        $date = new \DateTime();
        $form
                ->with('Please provide the new Access information:')
                ->add('user')
                ->add('client')
                ->add('region')
                ->add('country')
                ->add('all_countries', null, array('required' => false))
                ->end()


        ;
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
                ->add('id')
                ->add('user')
                ->add('client')
//->add('country')
        ;
    }

}
