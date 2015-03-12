<?php

namespace MissionControl\Bundle\UserBundle\Controller;

use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;
use MissionControl\Bundle\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
// For generating documentation:
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View;
// Preluate din controller exemplu:
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MissionControl\Bundle\CampaignBundle\Entity\Useraccess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use \Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\File\File;
use JMS\Serializer\SerializationContext;

class UserController extends FOSRestController {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * @Route("/users/registration", name="_users_registration")
     * @Method("POST")
     *
     * @ApiDoc(
     * 		resource = true,
     * 		description = "Registers a new user with given credentials.",
     * 		statusCodes = {
     * 			201 = "User added to the database.",
     * 			403 = "Returned when parameters used for registration are not valid."
     * 		},
     * 		parameters = {
     *                      {"name" = "active",    "dataType"="boolean",   "required"=true, "format"="true/false","description"="User is active or disabled."},
     *                      {"name" = "username",   "dataType"="text",      "required"=true, "description"="username description"},
     *                      {"name" = "lastname",   "dataType"="text",      "required"=true, "description"="lastname description"},
     *                      {"name" = "firstname",  "dataType"="text",      "required"=true, "description"="firstname description"},
     *                      {"name" = "email",      "dataType"="text",      "required"=true, "description"="email description"},
     *                      {"name" = "phone",      "dataType"="text",      "required"=true, "description"="phone description"},
     *                      {"name" = "title",      "dataType"="text",      "required"=true, "description"="title description"},
     *                      {"name" = "office",     "dataType"="text",      "required"=true, "description"="office description"},
     *                      {"name" = "profile_picture", "dataType"="text", "required"=true, "description"="profile_picture description"},
     *                      {"name" = "role_id", "dataType"="text", "required"=true, "description"="role for this user (1 , 2 or 3 )"},
     *                      {"name" = "password",   "dataType"="text",      "required"=true, "description"="password description"},
     *                      
     * 			
     * 		}
     * )
     *
     */
    public function postUsersAction(Request $request) {

        $response = new Response();
        $createDate = new \DateTime();
        $createDate->setTimezone(self::timezoneUTC());
        // Create User instance and set property values:
        $user = new User();


        $user->setEnabled($request->get('active'));
        $user->setUsername($request->get('username'));
        $user->setLastname($request->get('lastname'));
        $user->setFirstname($request->get('firstname'));
        $user->setEmail($request->get('email'));
        $user->setPhone($request->get('phone'));
        $user->setTitle($request->get('title'));
        $user->setOffice($request->get('office'));
        $user->setProfilepicture($request->get('profile_picture'));
        $user->setPassword($request->get('password'));



        if ($request->get('role_id') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Field role_id must have a value!'
            )));
            return $response;
        }

        $role_id = $request->get('role_id');

        $role = $this->getDoctrine()->getRepository('UserBundle:Role')->findOneById($role_id);
        if (!$role) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'You provided an invalid role id / No role_id provided.'
            )));
            return $response;
        }

        if ($request->get('active') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Field active must have a value!'
            )));
            return $response;
        }

        if ($request->get('username') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Field username must have a value!')));
            return $response;
        }

        $username_already_exists = $this->getDoctrine()->getRepository('UserBundle:User')->findOneByUsername($request->get('username'));
        if ($username_already_exists) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array('success' => false, 'message' => 'The username provided is already in use.')));
            return $response;
        }


        if ($request->get('lastname') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Field lastname must have a value!')));
            return $response;
        }
        if ($request->get('firstname') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Field firstname must have a value!')));
            return $response;
        }
        if ($request->get('email') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Field email must have a value!')));
            return $response;
        }
        if ($request->get('phone') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Field phone must have a value!')));
            return $response;
        }
        if ($request->get('title') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Field title must have a value!')));
            return $response;
        }
        if ($request->get('office') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Field office must have a value!')));
            return $response;
        }
        if ($request->get('profile_picture') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Field profile_picture must have a value!')));
            return $response;
        }
        if ($request->get('password') == null) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array('success' => false, 'message' => 'Field password must have a value!')));
            return $response;
        }



        $role_name = $role->getName();

        $user->addRole($role_name);

        $user->setCreatedAt($createDate);
        $user->setUpdatedAt($createDate);


        //   $response->headers->set('Content-Type', 'application/json');
        //  $serializer = $this->get('jms_serializer');
        // Get validator service to check for errors:
        $validator = $this->get('validator');
        $errors = $validator->validate($user);



////
        //IN ORDER TO BE A OK RESPONSE < HERE WE MUST RETURN A RESPONSE NOT A VIEW 


        if (count($errors) > 0) {

            // Return $errors in JSON format:
            $view = $this->view($errors, 400);
            return $this->handleView($view);
        } // End of IF errors check.


        $user->setPassword(md5($request->request->get('password')));
        $key = Uuid::uuid4()->toString();
        $user->setApiKey($key);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        
        
        ///SET THE USER TO HAVE USERACCESS TO TEMP_CLIENT
        $temp_client = $this->getDoctrine()->getRepository('CampaignBundle:Client')->findOneByName('temp_client');
        $global_region = $this->getDoctrine()->getRepository('CampaignBundle:Region')->findOneByName('Global');
        $temp_useraccess = new Useraccess();
        $temp_useraccess->setClient($temp_client);
        $temp_useraccess->setRegion($global_region);
        $temp_useraccess->setAllCountries(true);
        $temp_useraccess->setUser($user);
        $em->persist($temp_useraccess);
        
        
        
        //END SET USER FOR USERACCESS
        
        
        
        $em->flush();

        $response->setStatusCode(201);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'User added to the database.',
            
                ))
        );

        return $response;
    }

// End of postUsersAction() method.

    /**
     * @Route("/users/authentication", name="_users_authentication")
     * @Method("POST")
     *
     * @ApiDoc(
     * 		resource = true,
     * 		description = "Returns API KEY for requested user.",
     * 		statusCodes = {
     * 			201 = "Returned when the user exists in the database and an API KEY has been returned.",
     * 			400 = "Could not find a User with the given username and password."
     * 		},
     * 		parameters = {
     * 			{"name" = "username", "dataType"="text", "required"=true},
     * 			{"name" = "password", "dataType"="text", "required"=true}
     * 		}
     * )
     *
     */
    public function authenticationUsersAction(Request $request) {

        // Prepare Response configuration:
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        // Ask Users entity to check if user is in the database:
        $repository = $this->getDoctrine()->getRepository('MissionControl\Bundle\UserBundle\Entity\User');
        $user = $repository->findOneBy(array(
            'username' => $request->request->get('username'),
            'password' => md5($request->request->get('password')),
                )
        ); // Query for one user matching by username and password.
        // Return error information if user instance is not found:
        if (!$user) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Could not find a User with the given username and password.'
                    ))
            );
            return $response;
        }

        // Set status code and content:
        $response->setStatusCode(201);


        $roles = $user->getRoles();
        foreach ($roles as $role) {
            $db_role = $this->getDoctrine()->getRepository('UserBundle:Role')->findOneByName($role);
            if ($db_role) {
                $the_role_id = $db_role->getId();
                $the_role_name = $db_role->getName();
                $the_role_sysname = $db_role->getSystemname();
            }
        }

        // Retrieve API_KEY for $user:
        $key = $user->getApiKey();
        $response->setContent(json_encode(array(
            'API_KEY' => $key,
            'user_role_id' => $the_role_id,
            'user_role_name' => $the_role_name,
            'user_id' => $user->getId(),
                        )
        ));
        return $response;
    }

// End of authenticationUsersAction() method.

    /**
     * @Route("/api/v1/users/{user_id}/access ", name="_put_user_access")
     * @Method("PUT")
     *
     * @ApiDoc(
     * 		resource = true,
     * 		description = "Add a user access to campaigns combination.",
     * 		statusCodes = {
     * 			201 = "Returned when the update succeded.",
     * 			400 = {
     *              "Returned when user id is not valid.",
     *              "Returned when client id is not valid.",
     *              "Returned when region id is not valid.",
     *              "Returned when country id is not valid."
     *          },
     *          403 = {
     *              "Invalid country id provided.",
     *              "Country does not belong to specified region",
     *              "If you specify the country , you must set the all_countries flag to false",
     *              "This user already has access to ALL of the countries in the region",
     *              "This user already has access to the country's campaigns",
     *              "If all_countries for the region is false , you must set a country."
     *          }
     * 		},
     * 		parameters = {
     * 		
     *                {"name" = "client_id", "dataType"="integer", "required"=true},
     *                {"name" = "region_id", "dataType"="integer", "required"=true},
     *                {"name" = "country_id", "dataType"="integer", "required"=false},
     *                {"name" = "all_countries", "dataType"="boolean", "required"=true},
     *    
     * 			
     * 		}
     * )
     *
     */
    public function putUserAccessAction($user_id, Request $request) {

        $update_date = new \DateTime();
        $update_date->setTimezone(self::timezoneUTC());
        // Create User instance and set property values:
        $response = new Response();

        $user = $this->getDoctrine()->getRepository("UserBundle:User")->find($user_id);
        //Check that the user really exists. Else error.
        if (!$user) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid user id provided.'
            )));
            return $response;
        }

        $client_id = $request->get('client_id');
        $region_id = $request->get('region_id');
        $country_id = $request->get('country_id');
        $all_countries = $request->get('all_countries');

//        if (!$request->get('all_countries')){
//            $response->setStatusCode(400);
//            $response->setContent(json_encode(array(
//                'success' => false,
//                'message' => 'All countries must be set to true or false.'
//            )));
//            return $response;
//        }
        
        if ($all_countries == null) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'All countries must be set to true or false.'
            )));
            return $response;
        }

        $client = $this->getDoctrine()->getRepository('CampaignBundle:Client')->findOneById($client_id);
        $region = $this->getDoctrine()->getRepository('CampaignBundle:Region')->findOneById($region_id);
        $country = $this->getDoctrine()->getRepository('CampaignBundle:Country')->findOneById($country_id);
        //If no client , error!
        if (!$client) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid client id provided.'
            )));
            return $response;
        }
        //If no region , error!
        if (!$region) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid region id provided.'
            )));
            return $response;
        }

        //If country is set , check that it exists.
        if (isset($country_id)) {
            if (!$country) {
                $response->setStatusCode(403);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'Invalid country id provided.'
                )));
                return $response;
            }
        }
        // If country exists , check that it corresponds to the region inputed.
        if ($country) {
            $country_region_validation = false;
            $countries_in_region = $region->getCountries();
            $countries_in_region_array = array();
            foreach ($countries_in_region as $country_in_region) {
                $countries_in_region_array[] = $country_in_region;
            }
            if (in_array($country, $countries_in_region_array)) {
                $country_region_validation = true;
            }
            if (!$country_region_validation) {
                $response->setStatusCode(403);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'Country ' . $country->getName() . ' does not belong to specified region : ' . $region->getName() . '.'
                )));
                return $response;
            }

            if ($all_countries) {
                $response->setStatusCode(403);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'If you specify the country , you must set the all_countries flag to false'
                )));
                return $response;
            }
        }

        //IF WE GET UP TO HERE , WE ASSUME THAT THE INPUT IS VALID TO CONTINUE.
        //Instantiate entity manager

        $em = $this->getDoctrine()->getManager();
        //Instantiate a new Useraccess Entity
        //VERIFY THAT THE USER DOESN'T ALLREADY HAVE ACCESS TO ALL COUNTRIES FOR THE SPECIFIED REGION
        $user_has_access_to_all_countries = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $client,
            'region' => $region,
            'all_countries' => true,
        ]);

        if ($user_has_access_to_all_countries) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'This user already has access to ALL of the countries in the ' . $region->getName() . ' region',
            )));
            return $response;
        }

        $useraccess = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
            'user' => $user,
            'client' => $client,
            'region' => $region,
            'country' => $country,
        ]);

        if ($useraccess) {
            if ($country) {
                $response->setStatusCode(403);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'This user already has access to the ' . $country->getName() . ' campaigns',
                )));
                return $response;
            }
        }

        $useraccess = new Useraccess();
        $useraccess->setUser($user);
        $useraccess->setClient($client);
        $useraccess->setRegion($region);
        $useraccess->setAllCountries($all_countries);
        if (!$all_countries) {
            //IF THE ALL_COUNTRIES_FOR_REGION flag is FALSE , THEN THE COUNTRY IS A MUST !
            if (!$country) {
                //IF NO COUNTRY , ERROR
                $response->setStatusCode(403);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'If all_countries for the region is false , you must set a country.'
                )));
                return $response;
            }
        }
        //IF COUNTRY IS EXISTS , ASSIGN IT TO USERACCESS ENTITY 
        if ($country) {
            $useraccess->setCountry($country);
        }
        $em->persist($useraccess);
        $em->flush();

        $extramessage = 'All countries within this region.';
        if ($country) {
            $extramessage = ' For country : ' . $country->getName() . '';
        }

        $response->setStatusCode(201);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'User ' . $user->getUsername() . ' now has access to region ' . $region->getName() . ' . ' . $extramessage
                ))
        );
        return $response;
    }

    /**
     * @Route("api/v1/users/{user_id}/setrole ", name="_put_user_role")
     * @Method("PUT")
     *
     * @ApiDoc(
     * 		resource = true,
     * 		description = "Updates a user's role.",
     * 		statusCodes = {
     * 			201 = "Returned when the update succeded.",
     * 			403 = "Returned when parameters are not valid."
     * 		},
     * 		parameters = {
     * 		
     * 			{"name" = "role_id", "dataType"="text", "required"=true},
     * 			
     * 		}
     * )
     *
     */
    public function putUserSetroleAction($user_id, Request $request) {


        $update_date = new \DateTime();
        $update_date->setTimezone(self::timezoneUTC());
        // Create User instance and set property values:
        $response = new Response();

        $user = $this->getDoctrine()->getRepository("UserBundle:User")->find($user_id);
        //Check that the user really exists. Else error.
        if (!$user) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid user id provided.'
            )));
            return $response;
        }
        $role_id = $request->get('role_id');
        //Check that the requested role exists.
        $role = $this->getDoctrine()->getRepository('UserBundle:Role')->findOneById($role_id);
        if (!$role) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Invalid role id provided.'
            )));
            return $response;
        }
        $em = $this->getDoctrine()->getManager();

        if ($role_id == 3) {
            //IF THE USER WILL BE ADMINISTRATOR
            $all_clients = $this->getDoctrine()->getRepository('CampaignBundle:Client')->find(1);
            $global_region = $this->getDoctrine()->getRepository('CampaignBundle:Region')->find(1);
            $useraccess = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
                'user' => $user,
                'client' => $all_clients,
                'region' => $global_region,
            ]);
            if ($useraccess) {
                $response->setStatusCode(403);
                $response->setContent(json_encode(array(
                    'success'=> false,
                    'message' => 'This user is already a administrator / Already has administrator access in control_user_access'
                )));
                return $response;
            } else {
                $useraccess = new Useraccess();
                $useraccess->setUser($user);
                $useraccess->setClient($all_clients);
                $useraccess->setRegion($global_region);
                $useraccess->setAllCountries(true);
                $em->persist($useraccess);
            }

            //CHECK THAT THE USER HAS A ENTRY INTO USERACCESS , IF NOT , ADD IT FOR ALL USERS AND GLOBAL.
        } else {
            $all_clients = $this->getDoctrine()->getRepository('CampaignBundle:Client')->find(1);
            $global_region = $this->getDoctrine()->getRepository('CampaignBundle:Region')->find(1);
            $useraccess = $this->getDoctrine()->getRepository('CampaignBundle:Useraccess')->findOneBy([
                'user' => $user,
                'client' => $all_clients,
                'region' => $global_region,
            ]);
            if ($useraccess) {
                $em->remove($useraccess);
            }

            //CHECK IF THE USER HAS AN ENTRY FOR ALL USERS AND GOBAL ,IF SO, REMOVE IT.
        }

        $user->removeRole('ROLE_VIEWER');
        $user->removeRole('ROLE_CONTRIBUTOR');
        $user->removeRole('ROLE_ADMINISTRATOR');


        $role_name = $role->getName();
        $user->addRole($role_name);
        $em->flush();

        $response->setStatusCode(201);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'User ' . $user->getUsername() . ' now has the role ' . $role_name
                ))
        );
        return $response;
    }

    /**
     * @Route("api/v1/users/{user_id}/honeydata ", name="_put_user_honeydata")
     * @Method("PUT")
     *
     * @ApiDoc(
     * 		resource = true,
     * 		description = "Updates a user's honey data information.",
     * 		statusCodes = {
     * 			201 = "Returned when the honey data update succeded.",
     * 			403 = "Returned when parameters are not valid."
     * 		},
     * 		parameters = {
     * 		
     * 			{"name" = "profile_picture", "dataType"="text", "required"=false},
     *                  {"name" = "title", "dataType"="text", "required"=false},
     *                  {"name" = "office", "dataType"="text", "required"=false}, 
     *                  {"name" = "honey_user_uuid", "dataType"="text", "required"=false}, 				
     *                  {"name" = "honey_user_id", "dataType"="text", "required"=false}, 	
     * 		}
     * )
     *
     */
    public function putUserHoneydataAction($user_id, Request $request) {


        $update_date = new \DateTime();
        $update_date->setTimezone(self::timezoneUTC());
        // Create User instance and set property values:
        $response = new Response();

        $user = $this->getDoctrine()->getRepository("UserBundle:User")->find($user_id);
        //Check that the user really exists. Else error.
        if (!$user) {
            $response->setStatusCode(403);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'There is no user with that id.'
            )));
            return $response;
        }

        $updated_fields = 0;

        if (!empty($request->get('profile_picture'))) {
            $user->setProfilepicture($request->get('profile_picture'));
            $updated_fields++;
        }
        if (!empty($request->get('title'))) {
            $user->setTitle($request->get('title'));
            $updated_fields++;
        }
        if (!empty($request->get('office'))) {
            $user->setOffice($request->get('office'));
            $updated_fields++;
        }
        if (!empty($request->get('honey_user_id'))) {
            $user->setHoneyid($request->get('honey_user_id'));
            $updated_fields++;
        }
        if (!empty($request->get('honey_user_uuid'))) {
            $user->setHoneyuuid($request->get('honey_user_uuid'));
            $updated_fields++;
        }
        $em = $this->getDoctrine()->getManager();

        $em->flush();

        $response->setStatusCode(201);
        $response->setContent(json_encode(array(
            'success' => true,
            'message' => 'User ' . $user->getUsername() . ' honey data updated. ',
            'updated_fields' => $updated_fields
                ))
        );


        return $response;
    }

    // /**
    //  * 
    //  * @Route("api/v1/users/{user_id}/info ", name="_get_user_info")
    //  * @Method("GET")
    //  * @ApiDoc(
    //  *    resource = true,
    //  *    description = "ENTER DESCRIPTION [UNDEr HEAVY CONSTRUCTION DUE TO NEW ROLES REQUIREMENT AND CAMPAIGN VISIBLESTATE]",
    //  *    statusCodes = {
    //  *     200 = "Returned when ",
    //  *     403 = "Returned when user API KEY is not valid.",
    //  *     404 = {
    //  *         "Returned when"
    //  *     },
    //  *     500 = "Returned when no token was found in header"
    //  *    },
    //  *    requirements = {
    //  *      {"name"="user_id",     "dataType"="integer","requirement"="true", "description"="The user's unique id"},
    //  *      {"name" = "_format", "requirement" = "false" }
    //  *    }
    //  * )
    //  * @return array
    //  * @View()
    //  */
    // public function getUsersInfoAction($user_id, Request $request) {
    //     $response = new Response();
    //     $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneById($user_id);
    //     //CHECK THAT THE USER EXISTS IN THE SYSTEM
    //     if (!$user) {
    //         $response->setStatusCode(404);
    //         $response->setContent(json_encode(array('success' => false, 'message' => 'There is no user for that user_id.')));
    //         return $response;
    //     }
    //     $roles = $user->getRoles();
    //     foreach ($roles as $role) {
    //         $db_role = $this->getDoctrine()->getRepository('UserBundle:Role')->findOneByName($role);
    //         if ($db_role) {
    //             $the_role_id = $db_role->getId();
    //             $the_role_name = $db_role->getName();
    //             $the_role_sysname = $db_role->getSystemname();
    //         }
    //     }
    //     $primary_user_data = array(
    //         'user_id' => $user->getId(),
    //         'user_role_id' => $the_role_id,
    //         'user_role_name' => $the_role_name,
    //         'firstname' => $user->getFirstname(),
    //         'lastname' => $user->getLastname(),
    //         'email' => $user->getEmail(),
    //     );
    //     /////////////////////////////////////////////////////////////////////////////////////////////////
    //     /////////////////////////////////////////////////////////////////////////////////////////////////
    //     $all_the_tasks_of_this_user = array();
    //     $tasks_where_user_is_owner = $this->getDoctrine()->getRepository('TaskBundle:Task')->findBy(['owner' => $user]);
    //     foreach ($tasks_where_user_is_owner as $twuio) {
    //         if ($twuio->getCampaign()->getNotVisible() == false) {
    //             //ONLY ADD TASKS THAT HAVE THE CAMPAIGN VISIBILITY ENABLED (NOT_VISIBLE = FALSE)
    //             $all_the_tasks_of_this_user[] = $twuio->getId();
    //         }
    //     }
    //     $tasks_where_user_is_creator = $this->getDoctrine()->getRepository('TaskBundle:Task')->findBy(['createdby' => $user]);
    //     foreach ($tasks_where_user_is_creator as $twuic) {
    //         if ($twuic->getCampaign()->getNotvisible() == false) {
    //             //ONLY ADD TASKS THAT HAVE THE CAMPAIGN VISIBILITY ENABLED (NOT_VISIBLE = FALSE) 
    //             $all_the_tasks_of_this_user[] = $twuic->getId();
    //         }
    //     }
    //     //PRELUAM TOATE INTRARILE DIN TEAMMEMBER UNDE USERUL E REVIEWER        
    //     $teammembers = $this->getDoctrine()->getRepository('CampaignBundle:Teammember')->findBy(['member' => $user, 'is_reviewer' => true]);
    //     //For each campaign where the user is reviewer, grab the campaign's tasks array.
    //     $campaign_ids_where_user_is_reviewer = array();
    //     foreach ($teammembers as $teammember) {
    //         $campaign_ids_where_user_is_reviewer[] = $teammember->getCampaign()->getId();
    //     }
    //     $task_ids_of_all_tasks_where_user_is_reviewer_within_campaign = array();
    //     foreach ($campaign_ids_where_user_is_reviewer as $campaign_id) {
    //         $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneBy(
    //                 ['id' => $campaign_id,
    //                     'not_visible' => false,]
    //         );
    //         $tasks_of_this_campaign = $this->getDoctrine()->getRepository('TaskBundle:Task')->findBy(['campaign' => $campaign]);
    //         foreach ($tasks_of_this_campaign as $task) {
    //             $task_ids_of_all_tasks_where_user_is_reviewer_within_campaign[] = $task->getId();
    //         }
    //     }
    //     foreach ($task_ids_of_all_tasks_where_user_is_reviewer_within_campaign as $task_id) {
    //         $all_the_tasks_of_this_user[] = $task_id;
    //     }
    //     $unique_tasks_of_this_user = array_unique($all_the_tasks_of_this_user);
    //     $returned_task_data_array = array();
    //     foreach ($unique_tasks_of_this_user as $uniquetask) {
    //         $grabbed_task = $this->getDoctrine()->getRepository('TaskBundle:Task')->find($uniquetask);
    //         $status_changer = $grabbed_task->getStatuschangedby();
    //         $task_data = array(
    //             'campaign_id' => $grabbed_task->getCampaign()->getId(),
    //             'campaign_name' => $grabbed_task->getCampaign()->getName(),
    //             'task_id' => $grabbed_task->getId(),
    //             'task_name' => $grabbed_task->getTaskname()->getName(),
    //             'last_task_status' => $grabbed_task->getTaskstatus()->getName(),
    //             'last_task_message' => $grabbed_task->getTaskmessage() ? $grabbed_task->getTaskmessage()->getMessage() : null,
    //             'last_task_status_date' => $grabbed_task->getTaskstatus() ? date('Y-m-d', $grabbed_task->getTaskstatus()->getUpdatedat()->getTimestamp()) : null,
    //             'status_changer_user_id' => $status_changer ? $status_changer->getId() : null,
    //             'status_changer_first_name' => $status_changer ? $status_changer->getFirstname() : null,
    //             'status_changer_last_name' => $status_changer ? $status_changer->getLastname() : null,
    //             'status_changer_profile_picture' => $status_changer ? $status_changer->getProfilepicture() : null,
    //         );
    //         $returned_task_data_array[] = $task_data;
    //     }
    //     $response->setStatusCode(200);
    //     $response->setContent(json_encode(array(
    //         'user' => $primary_user_data,
    //         'tasks_data' => $returned_task_data_array
    //     )));
    //     return $response;
    // }
}
