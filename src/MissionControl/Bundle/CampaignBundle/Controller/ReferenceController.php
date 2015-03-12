<?php

namespace MissionControl\Bundle\CampaignBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
#Entities
use MissionControl\Bundle\CampaignBundle\Entity\Client;
# API documentation:
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View;

class ReferenceController extends FOSRestController {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

//    /**
//     * @ApiDoc(
//     *      description = "Method that retrieves the clients array",
//     * )
//     *
//     * @Route("api/v1/references/clients", name="_get_clients")
//     * @Method("GET")
//     */
//    public function getClientsAction() {
//
//        $clientsInfo = array(); // Create necessary variables.
//        // Retrieve available clients:
//        $clientRepository = $this->getDoctrine()->getRepository('CampaignBundle:Client');
//        $clients = $clientRepository->findAll();
//
//        // Return information on available clients:
//        foreach ($clients as $client) {
//
//            $clientsInfo[] = array(
//                'client_id' => $client->getId(),
//                'client_name' => $client->getName(),
//                'created_at' => date('Y-m-dTH:i:s', $client->getCreatedAt()->getTimestamp()),
//                'updated_at' => date('Y-m-dTH:i:s', $client->getUpdatedAt()->getTimestamp())
//            );
//        } // End of client foreach.
//        // Send JSON response back to client:
//        $response = new Response();
//        $response->setStatusCode(200)
//        ->headers->set('Content-Type', 'application/json');
//        $response->setContent(json_encode(array(
//            'success' => TRUE,
//            'clients' => $clientsInfo
//        )));
//
//        return $response;
//    }
//
//// End of getClients.
//    /**
//     * @ApiDoc(
//     * 		description="Call that inserts a new client into the database.",
//     *    statusCodes = {
//     *     201 = "Returned when a new client has been successfully inserted.",
//     *     403 = {
//     *          "Returned when there already is a client with that name.",
//     *          "Returned when the client_name is not specified in the request."},    
//     *     500 = "Returned when no token was found in header"
//     *    },
//     *   
//     *  parameters={
//     *       {"name"="client_name",  "dataType"="string","required"=true,"description"="none" },
//
//     *       }
//     * )
//     * @Route("/api/v1/references/clients", name="_post_client")
//     * @Method("POST")
//
//     */
//    public function postClientAction(Request $request) {
//
//        // Retrieve values from the POST request:
//        $clientName = $request->request->get('client_name');
//
//        $clientRepository = $this->getDoctrine()->getRepository('CampaignBundle:Client'); // Create client repository.
//        // Run validation routine on the POST request:
//        if (empty($clientName)) { // Request is not valid:
//            $clientExists = $clientRepository->findOneByName($clientName);
//
//            if (!empty($clientExists)) { // Client already exists:
//                $response = new Response;
//                $response->setStatusCode(403)
//                ->headers->set('Content-Type', 'application/json');
//
//                $response->setContent(json_encode(array(
//                    'success' => FALSE,
//                    'message' => 'Bad request. Client ' . $clientName . ' already exists.'
//                )));
//
//                return $response;
//            } // End of client exists IF.
//
//            $response = new Response;
//            $response->setStatusCode(403)
//            ->headers->set('Content-Type', 'application/json');
//
//            $response->setContent(json_encode(array(
//                'success' => FALSE,
//                'message' => 'Bad request. Please insert client_name.'
//            )));
//
//            return $response;
//        } else { // Check if Client name already exists:
//            $clientExists = $clientRepository->findOneByName($clientName);
//
//            if (!empty($clientExists)) { // Client already exists:
//                $response = new Response;
//                $response->setStatusCode(400)
//                ->headers->set('Content-Type', 'application/json');
//
//                $response->setContent(json_encode(array(
//                    'success' => FALSE,
//                    'message' => 'Bad request. Client ' . $clientName . ' already exists.'
//                )));
//
//                return $response;
//            } // End of client exists IF.
//        } // End of validation IF.
//        // Add data to new Client entity:
//        $client = new Client();
//        $client->setName($clientName);
//        $client->setCreatedAt(new \DateTime());
//        $client->setUpdatedAt(new \DateTime());
//
//        // Add Client record into the database:
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->persist($client);
//        $entityManager->flush();
//
//        // Note: Client entity still keeps data after record in database is created.
//        // Set client information:
//        $clientInfo[] = array(
//            'client_id' => $client->getId(),
//            'client_name' => $client->getName(),
//            'created_at' => date('Y-m-dTH:i:s', $client->getCreatedAt()->getTimestamp()),
//            'updated_at' => date('Y-m-dTH:i:s', $client->getUpdatedAt()->getTimestamp())
//        );
//
//        // Send JSON response back to client:
//        $response = new Response();
//        $response->setStatusCode(200)
//        ->headers->set('Content-Type', 'application/json');
//        $response->setContent(json_encode(array(
//            'success' => TRUE,
//            'message' => 'Client has been added.',
//            'clients' => $clientInfo
//        )));
//
//        return $response;
//    }
//
//// End of postClients.
//
//    /**
//     * @ApiDoc(
//     * 		description="Call that inserts a new division into the database.",
//     *    statusCodes = {
//     *     201 = "Returned when a new client has been successfully inserted.",
//     *     403 = {
//     *          "Returned when there already is a client with that name.",
//     *          "Returned when the client_name is not specified in the request."},    
//     *     500 = "Returned when no token was found in header"
//     *    },
//     *   
//     *  parameters={
//     *       {"name"="client_name",  "dataType"="string","required"=true,"description"="none",
//     *        "name"="division_name",  "dataType"="string","required"=true,"description"="none"  },
//     *       }
//     * )
//     * @Route("/api/v1/references/division", name="_post_division")
//     * @Method("POST")
//
//     */
//    public function postDivisionAction(Request $request) {
//
//        $clientName = $request->request->get('client_name');
//        $divisionName = $request->request->get('division_name');
//        $response = new Response();
//        //Validate the clientName
//
//        $client = $this->getDoctrine()->getRepository('CampaignBundle:Client')->findByName($clientName);
//        if (!$client) {
//            //Throw error response;
//            $response->setStatusCode(403);
//            $response->setContent(json_encode(array(
//                'success' => false,
//                'message' => 'Cannot add a division without an existing client !'
//            )));
//            return $response;
//        }
//
//        //Validate the new division
//
//        $division = $this->getDoctrine()->getRepository('CampaignBundle:Division')->findByName($divisionName);
//
//        if ($division) {
//            //Throw error response;
//            $response->setStatusCode(403);
//            $response->setContent(json_encode(array(
//                'success' => false,
//                'message' => 'There already is a division using the specified name.'
//            )));
//            return $response;
//        }
//        
//        
//    }

}
