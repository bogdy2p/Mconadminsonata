<?php

namespace MissionControl\Bundle\CampaignBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MissionControl\Bundle\CampaignBundle\Entity\Productline;
use MissionControl\Bundle\CampaignBundle\Form\ProductlineType;

/**
 * Productline controller.
 *
 * @Route("/productline")
 */
class ProductlineController extends Controller
{

    /**
     * Lists all Productline entities.
     *
     * @Route("/", name="productline")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CampaignBundle:Productline')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Productline entity.
     *
     * @Route("/", name="productline_create")
     * @Method("POST")
     * @Template("CampaignBundle:Productline:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Productline();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('productline_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Productline entity.
     *
     * @param Productline $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Productline $entity)
    {
        $form = $this->createForm(new ProductlineType(), $entity, array(
            'action' => $this->generateUrl('productline_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Productline entity.
     *
     * @Route("/new", name="productline_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Productline();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Productline entity.
     *
     * @Route("/{id}", name="productline_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CampaignBundle:Productline')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Productline entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Productline entity.
     *
     * @Route("/{id}/edit", name="productline_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CampaignBundle:Productline')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Productline entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Productline entity.
    *
    * @param Productline $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Productline $entity)
    {
        $form = $this->createForm(new ProductlineType(), $entity, array(
            'action' => $this->generateUrl('productline_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Productline entity.
     *
     * @Route("/{id}", name="productline_update")
     * @Method("PUT")
     * @Template("CampaignBundle:Productline:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CampaignBundle:Productline')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Productline entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('productline_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Productline entity.
     *
     * @Route("/{id}", name="productline_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CampaignBundle:Productline')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Productline entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('productline'));
    }

    /**
     * Creates a form to delete a Productline entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('productline_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
