<?php

namespace MissionControl\Bundle\UserBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;


class CRUDController extends Controller {

    public function cloneAction() {

        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $clonedObject = clone $object;
        $clonedObject->setUsername($object->getUsername() . " (Clone)");
        $clonedObject->setEmail($object->getEmail() . " (Clone)");
        
        $this->admin->create($clonedObject);
        $this->addFlash('sonata_flash_message', 'Cloned successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

}
