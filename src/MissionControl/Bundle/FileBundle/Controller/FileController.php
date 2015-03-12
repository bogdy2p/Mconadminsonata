<?php

namespace MissionControl\Bundle\FileBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Rhumsaa\Uuid\Uuid;
use Rhumsaa\Uuid\Exception\UnsatisfiedDependencyException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
# Entities:
use MissionControl\Bundle\FileBundle\Entity\File as ProjectFile;
use MissionControl\Bundle\CampaignBundle\Entity\Campaign;
use MissionControl\Bundle\CampaignBundle\Model\FileType;
# Filesystem:
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
# API documentation:
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class FileController extends FOSRestController {

    public function timezoneUTC() {
        return new \DateTimeZone('UTC');
    }

    /**
     * @Route("/api/v1/campaigns/{campaignId}/files", name="_campaign_files_get")
     * @Method("GET")
     *
     * @ApiDoc(
     *      description="Returns information for the files related to a campaign.",
     *      statusCodes={
     *          200 = "OK",
     *          403 = "Invalid API KEY",
     *          404 = "Campaign not found (Invalid id)",
     *          500 = "Header x-wsse does not exist",
     *      },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign ID for selected campaign."}
     *      }
     * )
     */
    public function getCampaignFilesDataAction($campaignId) {
        $response = new Response;
        if (isset($campaignId)) { // If campaign was specified:
            $filesArray = array(); // Initialize a storage array for files.
            // Create instance of the Campaign entity:
            $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);

            if (!$campaign) {
                $response->setStatusCode(404);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => "Campaign id you provided is invalid."
                )));
                return $response;
            }
            // Retrieve original names of the files:
            $em = $this->getDoctrine()->getManager();
            $originalNames = $em->getRepository('FileBundle:File')->getAllOriginalNames($campaign);

            // Set data to be returned for each file:
            foreach ($originalNames as $originalName) {

                if ($originalName->getContentType() != '.mtrx' AND $originalName->getContentType() != '.uld') {

                    // Return latest version for the file:
                    $latestFileVersion = $em->getRepository('FileBundle:File')->getLatestFileVersion($campaign, $originalName);

                    if ($latestFileVersion) {

                        $filesArray[] = array(
                            'campaignId' => $latestFileVersion->getCampaign()->getId(),
                            'TaskName' => $latestFileVersion->getTask() ? $latestFileVersion->getTask()->getTaskname()->getName() : null,
                            'FileId' => $latestFileVersion->getUuid(),
                            'FileName' => $latestFileVersion->getOriginalName(),
                            'FileType' => $latestFileVersion->getFileType() ? $latestFileVersion->getFileType()->getName() : null,
                            'FileVersion' => $latestFileVersion->getVersion() ? $latestFileVersion->getVersion() : null,
                            'FilePath' => $latestFileVersion->path,
                            'FileModifiedDate' => date('Y-m-dTH:i:s', $latestFileVersion->getUpdatedAt()->getTimestamp()),
                            'FileModifiedBy' => $latestFileVersion->getUser() ? $latestFileVersion->getUser()->getFirstname() . ' ' . $latestFileVersion->getUser()->getLastname() : 'Matrix',
                        );
                    }
                } // End of content_type check IF.
            } // End of $files foreach().
            // Set response data:

            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array(
                'files' => $filesArray
                            )
            ));

            return $response;
        } // End of $campaign IF().
    }

// End of GET campaign files information method().

    /**
     * @Route("/api/v1/campaigns/{campaignId}/tasks/{taskId}/files", name="_get_task_files_info")
     * @Method("GET")
     *
     * @ApiDoc(
     *      description="Returns information for the files related to the task of a campaign.",
     *      statusCodes={
     *      200 = "OK",
     *      400 = "Request not specified campaign id and task id",
     *      403 = "Returned when the API KEY is not valid",
     *      404 = {"Returned when no campaign was found for the provided id","Returned when no Task was found for the provided ID"},
     *      500 = "Returned when a token was not found in headers",
     * },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign ID for selected campaign."},
     *          {"name"="taskId", "dataType"="string", "description"="Task ID for the selected task."}
     *      }
     * )
     */
    public function getTaskFilesDataAction($campaignId, $taskId) {

        $response = new Response;
        $filesArray = array(); // Initialize a storage array for files.

        if (!isset($campaignId, $taskId)) { // If campaign and task were not specified:
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Request could not be made. Please specify campaignId and taskId.'
                            )
            ));

            return $response;
        } // End of input validation IF.
        // Create instance of Campaign and Task entity:
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);
        if (!$campaign) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => "No campaign for the provided ID."
            )));
            return $response;
        }
        $task = $this->getDoctrine()->getRepository('TaskBundle:Task')->find($taskId);
        if (!$task) {
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => "No task for the provided ID."
            )));
            return $response;
        }
        // Retrieve original names of the files of the task:
        $em = $this->getDoctrine()->getManager();
        $originalNames = $em->getRepository('FileBundle:File')->getAllTaskOriginalNames($campaign, $task);

        // Set data to be returned for each file:
        foreach ($originalNames as $originalName) {

            if ($originalName->getContentType() != '.mtrx' AND $originalName->getContentType() != '.uld') {

                // Return latest version for the file:
                $latestFileVersion = $em->getRepository('FileBundle:File')->getLatestFileVersion($campaign, $originalName);

                if ($latestFileVersion) {

                    $filesArray[] = array(
                        'campaignId' => $latestFileVersion->getCampaign()->getId(),
                        'TaskName' => $latestFileVersion->getTask() ? $latestFileVersion->getTask()->getTaskname()->getName() : null,
                        'FileId' => $latestFileVersion->getUuid(),
                        'FileName' => $latestFileVersion->getOriginalName(),
                        'FileType' => $latestFileVersion->getFileType() ? $latestFileVersion->getFileType()->getName() : null,
                        'FileVersion' => $latestFileVersion->getVersion() ? $latestFileVersion->getVersion() : null,
                        'FilePath' => $latestFileVersion->path,
                        'FileModifiedDate' => date('Y-m-dTH:i:s', $latestFileVersion->getUpdatedAt()->getTimestamp()),
                        'FileModifiedBy' => $latestFileVersion->getUser() ? $latestFileVersion->getUser()->getFirstname() . ' ' . $latestFileVersion->getUser()->getLastname() : 'Matrix',
                    );
                }
            } // End of content_type check IF.
        } // End of $files foreach().
        // Set response data:
        $response = new Response;
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode(array(
            'files' => $filesArray
                        )
        ));

        return $response;
    }

// End of GET task files info method().

    /**
     * @Route("/api/v1/campaigns/{campaignId}/downloadfile", name="_download_file_by_type")
     * @Method("GET")
     *
     * @ApiDoc(
     *      description="Downloads a campaign file identified by task and file type.",
     *      statusCodes={
     *          200 = "Returned when the request is without errors",
     *          400 = {
     *                "Returned when no input for task_name_id or file_type_id",
     *                "Returned when requested file does not exist"
     *                 },
     *          403 = "Invalid API KEY",
     *          500 = "Header x-wsse does not exist",
     *      },
     *      requirements={
     *          {"name"="task_name_id", "dataType"="string", "description"="Task name identifier."},
     *          {"name"="file_type_id", "dataType"="string", "description"="File type identifier."}
     *      }
     * )
     */
    public function downloadLatestFileVersionAction($campaignId, Request $request) {

        // Validate request inputs:
        $taskNameId = $request->get('task_name_id');
        $fileTypeId = $request->get('file_type_id');

        if (empty($taskNameId) && empty($fileTypeId)) { // Request does not contain necessary data.
            // Return error message to client:
            $response = new Response();
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array(
                'success' => FALSE,
                'message' => 'Bad request. Please input values for task_name_id, file_type_id.'
                            )
            ));

            return $response;
        } // End of input validation IF.
        // Retrieve campaign data:
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);

        // Retrieve requested file data from database:
        $taskName = $this->getDoctrine()->getRepository('TaskBundle:Taskname')->find($taskNameId);
        $taskQuery = $this->getDoctrine()->getRepository('TaskBundle:Task')->createQueryBuilder('t')
                ->where('t.campaign = :campaign AND t.taskname = :taskName')
                ->setParameter('campaign', $campaign)
                ->setParameter('taskName', $taskName)
                ->getQuery();
        $task = $taskQuery->getOneOrNullResult();

        $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->find($fileTypeId);
        $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                ->where('f.task = :task AND f.fileType = :fileType AND f.notVisible != TRUE')
                ->orderBy('f.updatedAt', 'DESC')
                ->setMaxResults(1)
                ->setParameter('task', $task)
                ->setParameter('fileType', $fileType)
                ->getQuery();
        $file = $fileQuery->getOneOrNullResult();

        // Check if file exists:
        if (!isset($file)) { // File does not exist.
            // Return error message to client:
            $response = new Response();
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array(
                'success' => FALSE,
                'message' => 'Bad request. Requested file does not exist.'
                            )
            ));

            return $response;
        } // End of file validation IF.
        // If file exists, send file back to client as disposition attachment:
        $response = new Response(file_get_contents($file->path));

        // Set headers:
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getOriginalName());
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Length', filesize($file->path));

        $fileMimeType = new File($file->path);
        $fileMimeType = $fileMimeType->getMimeType();
        $response->headers->set('Content-Type', $fileMimeType);

        // Send response to client:
        return $response;
    }

// End of GET latest version of a file method().

    /**
     * @Route("/api/v1/files/{fileId}", name="_download_campaign_file")
     * @Method("GET")
     *
     * @ApiDoc(
     *      description="Downloads (as attachment) a support file related to a campaign.",
     *      statusCodes={
     *      200 = "OK",
     *      400 = {"Returned when fileID in the URL does not have a value or is incorrect.", "Returned when file already set to not_visible."},
     *      403 = "Invalid API KEY",
     *      500 = "Header x-wsse does not exist", 
     *      },
     *      requirements={
     *          {"name"="fileId", "dataType"="string", "description"="File identifier for the file to be downloaded."}
     *      },
     * )
     *
     */
    public function downloadCampaignFileAction($fileId) {

        if (empty($fileId)) {

            $response = new Response();
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array(
                'success' => FALSE,
                'message' => 'Request could not be made. Please input the fileID in the URL.'
                            )
            ));

            return $response;
        } // End of file ID IF.
        // Return file repository:
        $file = $this->getDoctrine()->getRepository('FileBundle:File')->find($fileId);

        // Check if the file exists:
        if (empty($file)) {

            $response = new Response();
            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode(array(
                'success' => FALSE,
                'message' => 'File with ID ' . $fileId . ' does not exist. Please ensure file ID is correct.'
                            )
            ));

            return $response;
        } // End of file IF.

        if ($file->getNotVisible() == TRUE) { // File is not visible.
            $content = json_encode(array(
                'success' => FALSE,
                'message' => 'Bad request. File cannot be downloaded, not_visible set to TRUE'
            ));
            $headers = array(
                'Content-Type', 'application/json'
            );

            return new Response($content, 400, $headers);
        }

        // Prepare file to be returned to client:
        $content = new File($file->path);
        $response = new Response(file_get_contents($file->path));

        // Specify content disposition header:
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getOriginalName());
        $response->headers->set('Content-Disposition', $disposition);

        // Specify file MIME type:
        $fileMimeType = new File($file->path);
        $fileMimeType = $fileMimeType->getMimeType();
        $response->headers->set('Content-Type', $fileMimeType);

        return $response;
    }

// End of downloadCampaignFileAction() method.

    /**
     * @Route("/api/v1/campaigns/{campaignId}/files", name="_campaign_files_post")
     * @Method("POST")
     *
     * @ApiDoc(
     *      description="Uploads a support file related to a campaign.",
     *      statusCodes={
     *          200 = "OK",
     *          400 = {"Please input values for task_id, file_type_id.", "Please specify file to be uploaded."},
     *          403 = "Invalid API KEY",
     *          500 = "Header x-wsse does not exist", 
     *      },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Campaign identifier for uploading files."}
     *      },
     *      parameters={
     *          {"name"="campaign_file", "dataType"="file", "required"=true, "description"="The file to be uploaded."},
     *          {"name"="task_id", "dataType"="string", "required"=true, "description"="The task related to the uploaded file."},
     *          {"name"="file_type_id", "dataType"="string", "required"=true, "description"="The file type of the uploaded file."}
     *      }
     * )
     *
     */
    public function postCampaignFilesAction($campaignId, Request $request) {

        if (strpos(get_class($request->files->get('campaign_file')), 'UploadedFile') != FALSE) { // File has been sent in the request:

            if ( !$request->get('task_id') || !$request->get('file_type_id') ) {

                $response = new Response();
                $response->setStatusCode(400);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'Please input values for task_id, file_type_id.'
                    )
                ));

                return $response;
            
            } 

        } else { // File was not specified:

            $response = new Response();
                $response->setStatusCode(400);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'Please specify file to be uploaded.'
                    )
                ));

            return $response;

        } // End of validation IF.

        // Populate file instance:
        $file = new ProjectFile();

        $file->setCampaignFile($request->files->get('campaign_file'));
        $file->setUuid(Uuid::uuid4()->toString());
        $file->setOriginalName(rawurldecode($file->getFile()->getClientOriginalName()));
        $file->setContentType($file->getFileExtension($file->getOriginalName()));
        $file->setFileLength($file->getFile()->getClientSize());
        $file->setFileName($file->getUuid() . $file->getContentType());
        $file->setNotVisible(FALSE);

        // Set time for when the file was created:
        $creationDate = new \DateTime();
        $creationDate->setTimezone(self::timezoneUTC());

        $file->setCreatedAt($creationDate);
        $file->setUpdatedAt($creationDate);

        // Specify designated campaign:
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneBy(array('id' => $campaignId));
        $file->setCampaign($campaign);

        // Return designated task specified in request (task should already exist in database):
        $task = $this->getDoctrine()->getRepository('TaskBundle:Task')->findOneBy(array('id' => $request->get('task_id')));
        $file->setTask($task);

        // Specify Task Type:
        $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneBy(array('id' => $request->get('file_type_id')));
        $file->setFileType($fileType);

        // Specify User Creator:
        $userCreator = $this->getUser();
        $file->setUser($userCreator);

        $response = new Response(); // Create and prepare Response object to be sent back to client.
        $response->headers->set('Content-Type', 'application/json');

        // Retrieve latest version of the file:
        $fileRepository = $this->getDoctrine()->getRepository('FileBundle:File');
        $versionQuery = $fileRepository->createQueryBuilder('f')
                ->where('f.campaign = :campaign 
                AND f.originalName = :originalName
                AND f.task = :task
                AND f.fileType = :fileType
                AND f.contentType = :contentType
                AND f.version IS NOT NULL
                AND f.notVisible != TRUE
                ')
                ->setParameter('campaign', $campaign)
                ->setParameter('originalName', $file->getOriginalName())
                ->setParameter('task', $task)
                ->setParameter('fileType', $fileType)
                ->setParameter('contentType', $file->getContentType())
                ->orderBy('f.version', 'DESC')
                ->setMaxResults(1)
                ->getQuery();

        $latestFileVersion = $versionQuery->getOneOrNullResult(); // Return latest version or NULL if file name does not match.

        if ($latestFileVersion != null) { // File already exists.
            $file->setVersion($latestFileVersion->getVersion() + 1);
        } else {

            $file->setVersion(1);
        } // End of version IF().
        // Get validator service to check for errors:
        $validator = $this->get('validator');
        $errors = $validator->validate($file);

        // Validate the values entered in the File object:
        if (count($errors) > 0) {

            // Return $errors in JSON format:
            $serializer = $this->get('jms_serializer');
            $serializer->serialize($errors, 'json');

            $response->setContent($errors);

            return $response;
        } // End of errors IF.
        // Persis object to database:
        $em = $this->getDoctrine()->getManager();

        $targetDir = 'campaign_files/' . $campaign->getId(); // Specify target directory for file.
        $file->upload($targetDir); // Move campaign file in target directory.

        $em->persist($file);


        //RECALCULATE AND ASSIGN CAMPAIGN COMPLETENESS AFTER POSTING EACH FILE.

        $new_campaign_completeness = self::recalculate_campaign_completeness($campaign);
        $campaign->setCompleteness($new_campaign_completeness);



        //END RECALCULATION

        $em->flush();

        $response->setContent(json_encode(array(
            'success' => true
                        )
        ));

        return $response;
    }

// End of POST campaign file method().

    /**
     * 
     * @Route("/api/v1/files/{fileId}", name="_disable_file")
     * @Method("PUT")
     *
     * @ApiDoc(
     *      description="File management - Sets a file to not visible",
     *      statusCodes={
     *          200 = "OK",
     *          400 = {"Bad request. File does not exist.", "Bad request. File not_visible already set to TRUE.", "Bad request. Not the latest version of the file."},
     *          403 = "Invalid API KEY",
     *          500 = "Header x-wsse does not exist",
     *      },
     *      requirements={
     *          {"name"="fileId", "dataType"="string", "description"="File ID"}
     *      }
     * )
     */
    
    public function setFileVisibilityAction($fileId) {

        // Retrieve file record:
        $fileManager = $this->getDoctrine()->getManager();
        $file = $fileManager->getRepository('FileBundle:File')->find($fileId);

        if (empty($file)) { // File does not exist.
            $content = json_encode(array(
                'success' => FALSE,
                'message' => 'Bad request. File does not exist.'
            ));
            $headers = array(
                'Content-Type', 'application/json'
            );

            return new Response($content, 400, $headers);
        } // End of file validation IF.

        if ($file->getNotVisible() == TRUE) { // File already set to NOT visible
            $content = json_encode(array(
                'success' => FALSE,
                'message' => 'Bad request. File not_visible already set to TRUE.'
            ));
            $headers = array(
                'Content-Type', 'application/json'
            );

            return new Response($content, 400, $headers);
        } // End of visibility check IF.
        // Check if version is the latest:
        $latestFileVersion = $fileManager->getRepository('FileBundle:File')->getLatestFileVersion($file->getCampaign(), $file);
        if ($file->getVersion() < $latestFileVersion->getVersion()) { // Not the latest version of the file. Cannot be set to not_visible.
            $content = json_encode(array(
                'success' => FALSE,
                'message' => 'Bad request. Not the latest version of the file.'
            ));
            $headers = array(
                'Content-Type', 'application/json'
            );

            return new Response($content, 400, $headers);
        } // End of version check IF.
        // If file exists and visibility not yet set to TRUE:
        $file->setNotVisible(TRUE);
        $file->setVersion(NULL);
        // Set time for when the file was created:
        $updateDate = new \DateTime();
        $updateDate->setTimezone(self::timezoneUTC());
        $file->setUpdatedAt($updateDate);

        $fileManager->flush(); // Update file record in database;
        // Return successful response message:
        $content = json_encode(array(
            'success' => TRUE,
            'message' => 'Request completed. File not_visible set to TRUE.'
        ));
        $headers = array(
            'Content-Type', 'application/json'
        );

        return new Response($content, 200, $headers);
    }

    /**
     * @Route("/api/v1/campaigns/{campaignId}/matrixfile", name="_files_get")
     * @Method("GET")
     *
     * @ApiDoc(
     *      description="Retrieves information about the campaign's matrix file.",
     *      statusCodes={
     *      200 = "OK",
     *      204 = "Matrix file has not yet been created",
     *      403 = "Invalid API KEY",
     *      404 = "Requested campaign is not available in our database",
     *      500 = "Header x-wsse does not exist",
     *      },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Unique identifier for the campaign for which the files information to be retrived."}
     *      }
     * )
     *
     */
    public function getMatrixFileAction($campaignId, Request $request) {

        $user = $this->getUser();

        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')
                ->find($campaignId);

        $response = new Response();

        if (!$campaign) {
            // Set response data:
            $response->setStatusCode(404);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Requested campaign is not available in our database.'
                    ))
            );
            return $response;
        }

        $file = $this->getDoctrine()->getRepository('FileBundle:File')
                ->findOneBy(array(
            'campaign' => $campaign,
            'uuid' => $campaign->getMatrixfileUuid()
                )
        );

        if (!$file) {
            // Set response data:
            $response->setStatusCode(204);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Matrix file has not yet been created.'
                    ))
            );
            return $response;
        }

        $response->setStatusCode(200);
        $response->setContent(json_encode(array(
            'success' => true,
            'CampaignId' => $file->getCampaign()->getId(),
            'Token' => $campaign->getToken(),
            'FileId' => $file->getUuid(),
            'FileName' => $file->getOriginalName(),
            'FileLength' => $file->getFileLength(),
            'file_length' => $file->getFileLength(),
            'FileVersion' => $file->getVersion() ? $file->getVersion() : null,
            'FilePath' => $file->path,
            'FileModifiedDate' => date('Y-m-dTH:i:s', $file->getUpdatedAt()->getTimestamp()),
            'FileModifiedBy' => $file->getUser() ? $file->getUser()->getFirstname() . ' ' . $file->getUser()->getLastname() : 'Matrix'
                ))
        );

        return $response;
    }

// End of getMatrixFileAction() method.

    /**
     * @Route("/api/v1/projects/{campaignId}/files/{fileId}", name="_file_get")
     * @Method("GET")
     *
     * @ApiDoc(
     *      description="Retrieves the matrix file attributed to the campaign.",
     *      statusCodes={
     *      200 = "OK",
     *      400 = "Bytes of data could not be encoded in base64.",
     *      403 = "Invalid API KEY",
     *      500 = "Header x-wsse does not exist",
     * },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Unique identifier for the campaign for which the files information to be retrived."},
     *          {"name"="fileId", "dataType"="string", "description"="Unique identifier for the file that is being requested."}
     *      },
     *      parameters={
     *          {"name"="offset", "dataType"="text", "required"=true, "description"="Used for representing the current chunk being downloaded."},
     *          {"name"="byte_count", "dataType"="text", "required"=true, "description"="Size of current chunk being downloaded."}
     *      }
     * )
     *
     */
    public function getFileAction($campaignId, $fileId, Request $request) {

        // Retrieve repository for specified file:
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->find($campaignId);
        $file = $this->getDoctrine()->getRepository('FileBundle:File')
                ->findOneBy(array(
            'campaign' => $campaign,
            'uuid' => $fileId
                )
        );

        // Open file and read amount of bytes:
        $filePointer = fopen($this->get('kernel')->getRootDir() . '/../web/' . $file->getPath(), "rb");
        stream_set_blocking($filePointer, 0);
        // Move pointer position to specified offset:
        fseek($filePointer, $request->get('offset'));

        // Read specified chunk of data:
        $bytes = fread($filePointer, $request->get('byte_count'));
        fclose($filePointer);

        $response = new Response(); // Instantiate response.
        // Encode $bytes data in base64:
        $bytes = base64_encode($bytes);

        if (!$bytes) {
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Bytes of data could not be encoded in base64.'
                            )
            ));
        } // End of $bytes IF.

        $response->setStatusCode(200);
        $response->setContent($bytes);

        return $response;
    }

// End of getFileAction() method.

    /**
     * @Route("api/v1/projects/{campaignId}/files", name="_file_post")
     * @Method("POST")
     *
     * @ApiDoc(
     *      description="Uploads a file incrementally using chunks of data.",
     *      statusCodes={
     *      200 = "OK",
     *      400 = {"Current chunk size not equal to bytes_count.", "Bytes data could not be decoded.", "File is not yet created.", "File already exists."},
     *      403 = "Invalid API KEY",
     *      500 = "Header x-wsse does not exist",
     * },
     *      requirements={
     *          {"name"="campaignId", "dataType"="string", "description"="Unique identifier for the campaign to which the file to be uploaded."}
     *      },
     *      parameters={
     *          {"name"="file_name", "dataType"="text", "required"=true, "description"="Full name of client file with extension."},
     *          {"name"="temp_uuid", "dataType"="text", "required"=true, "description"="Will be used for creating temporary file on first call and in the following uploads."},
     *          {"name"="file_length", "dataType"="text", "required"=true, "description"="Will be used for signaling last chunk upload call, and to set move action to permanent directory."},
     *          {"name"="offset", "dataType"="text", "required"=true, "description"="Used for identifying the chunk that is currently being uploaded."},
     *          {"name"="bytes_count", "dataType"="text", "required"=true, "description"="Current chunk size."},
     *          {"name"="bytes", "dataType"="text", "required"=true, "description"="Current chunk data."}
     *      }
     * )
     *
     */
    public function postFileAction($campaignId, Request $request) {

        // Retrieve data from Request sent by client:
        $fileName = $request->request->get('file_name');
        $tmpUuid = $request->request->get('temp_uuid');
        $fileLength = $request->request->get('file_length');
        $offset = $request->request->get('offset');
        $bytesCount = $request->request->get('bytes_count'); // Size of current encoded chunk.
        $bytes = base64_decode($request->request->get('bytes')); // Decode bytes data from base64.
        // Create response object:
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        // Check if decoding was successful:
        if ($bytes) {

            if (strlen($bytes) != $bytesCount) { // Bytes size does not match bytes_count.
                $response->setStatusCode(400);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'Current chunk size not equal to bytes_count.'
                                )
                ));

                return $response;
            }
        } else { // Data could not be decoded.
            $response->setStatusCode(400);
            $response->setContent(json_encode(array(
                'success' => false,
                'message' => 'Bytes data could not be decoded.'
                            )
            ));

            return $response;
        } // End of $bytes IF.
        // Initiate Filesystem:
        $fs = new Filesystem();

        // Validate file input and direct flow:
        if (!$fs->exists(array(__DIR__ . '/../../../../../web/uploads/tmp/' . 'tmp_' . $tmpUuid . '.tmp'))) { // File is not yet created.
            if ($offset == 0) { // Create temporary file for storing chunks:
                $tmpFile = fopen(__DIR__ . '/../../../../../web/uploads/tmp/' . 'tmp_' . $tmpUuid . '.tmp', 'a+');
                fseek($tmpFile, $offset);
                fwrite($tmpFile, $bytes);
                fclose($tmpFile);

                // Return confirmation message of creating new temporary file.
                $response->setStatusCode(200);
                $response->setContent(json_encode(array(
                    'success' => true,
                    'message' => 'File ' . 'tmp_' . $tmpUuid . '.tmp' . ' has been created. Chunk offset(' . $offset . ') was added to the beginning of the file.')));

                return $response;
            } elseif ($offset > 0) { // Chunk offset not indicating start of file (first chunk).
                // Return error message in JSON format.
                $response->setStatusCode(400);
                $response->setContent(json_encode(array(
                    'success' => false,
                    'message' => 'File is not yet created. Chunk offset(' . $offset . ') not indicating start of file content.')));

                return $response;
            }  // End of offset check IF.
        } else { // If file already exists.
            if ($offset > 0) { // Append data to already existing file:
                $tmpFile = fopen(__DIR__ . '/../../../../../web/uploads/tmp/' . 'tmp_' . $tmpUuid . '.tmp', 'a+');
                fseek($tmpFile, $offset);
                $ok = fwrite($tmpFile, $bytes);
                fclose($tmpFile);

                // Check if file length is indicating if entire file has been uploaded.
                if ($fileLength <= filesize(__DIR__ . '/../../../../../web/uploads/tmp/' . 'tmp_' . $tmpUuid . '.tmp')) {

                    // Set necessary data for making the file permanent:
                    $tmpData = array(
                        'fileName' => $fileName,
                        'tmpUuid' => $tmpUuid,
                        'fileLength' => $fileLength
                    );

                    // Move file in permanent folder and persist File entity to database:
                    list($data, $ok) = $this->moveFileAction($campaignId, $tmpData);

                    // Check if moveFIleAction() completed successfully:
                    if (!$ok) { // File data did not pass validation:
                        // Return error messages to user:
                        $response->setStatusCode(400);

                        return $this->render('FileBundle::errors.json.twig', array('errors' => $errors), $response);
                    } // End of $ok validation IF.                 
                    // Return JSON success message of moving file to permanent folder:
                    // Note: $data variable, if moveFileAction() is successful, will return File Name.
                    $response->setStatusCode(200);
                    $response->setContent(json_encode(array(
                        'success' => true,
                        'FileUuid' => $data
                    )));

                    return $response;
                } // End of last chunk upload call IF.

                if (!$ok) { // Chunk was not appended to the temporary file!
                    // Return error message information:
                    $response->setStatusCode(400);
                    $response->setContent(json_encode(array(
                        'success' => false,
                        'message' => 'Chunk with offset ' . $offset . ' was not appended to the temporary file.')));

                    return $response;
                } // End of fwrite() validation IF.
                // Return error message information:
                $response->setStatusCode(200);
                $response->setContent(json_encode(array(
                    'success' => true,
                    'message' => 'Chunk with offset ' . $offset . ' was appended to temporary file: ' . 'tmp_' . $tmpUuid . '.tmp')));

                return $response;
            } elseif ($offset == 0) { // Chunk offset indicating start of data, file already exists.
                // Return error message in JSON format.
                $response->setStatusCode(400);
                $response->setContent(json_encode(array(
                    'success' => true,
                    'messsage' => 'File already exists. Chunk offset(' . $offset . ') is indicating start of file content.')));

                return $response;
            } // End of offset check IF.
        } // End of filesystem IF.
    }

// End of postFileAction method().

    /**
     * This method moves a uploaded file into a permanent folder.
     */
    public function moveFileAction($campaignId, $tmpData) {

        // Create an instance of the Campaign:
        $campaign = $this->getDoctrine()->getRepository('CampaignBundle:Campaign')->findOneById($campaignId);

        // Populate the File object with data:
        $file = new ProjectFile();
        // Set temporary file to be of UploadedFile type:
        $temporaryFile = new File(__DIR__ . '/../../../../../web/uploads/tmp/' . 'tmp_' . $tmpData['tmpUuid'] . '.tmp');

        $file->setFile($temporaryFile);
        $file->setUuid(Uuid::uuid4()->toString());
        $file->setOriginalName($tmpData['fileName']);
        $file->setContentType($file->getFileExtension($file->getOriginalName()));
        $file->setFileLength($tmpData['fileLength']);
        $file->setFileName($file->getUuid() . $file->getContentType());
        $file->setNotVisible(FALSE);

        // Specify User Creator:
        $file->setUser($this->getUser());

        // Set time for when the file was created:
        $creationDate = new \DateTime();
        $creationDate->setTimezone(self::timezoneUTC());

        $file->setCreatedAt($creationDate);
        $file->setUpdatedAt($creationDate);

        // Set Matrix file version and uuid:
        if ($campaign->getMatrixfileUuid() == null) { // Matrix file not yet created.
            $file->setVersion(1);
            $campaign->setMatrixfileUuid($file->getUuid());
            $campaign->setMatrixfileVersion($file->getVersion());
        } else { // Increment matrix file version.
            // Retrieve latest version number of matrix file for this campaign:
            $fileRepository = $this->getDoctrine()->getRepository('FileBundle:File');
            $versionQuery = $fileRepository->createQueryBuilder('f')
                    ->where('f.campaign = :campaign and f.task IS NULL')
                    ->setParameter('campaign', $campaign)
                    ->orderBy('f.version', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery();

            $latestMatrixVersion = $versionQuery->getSingleResult()->getVersion();

            // Set file version:
            $file->setVersion($latestMatrixVersion + 1);
            $campaign->setMatrixfileUuid($file->getUuid());
            $campaign->setMatrixfileVersion($file->getVersion());
        } // End of version IF.

        $file->setCampaign($campaign);

        // Get validator service to check for errors:
        $validator = $this->get('validator');
        $errors = $validator->validate($file);

        $response = new Response(); // Create and prepare Response object to be sent back to client.
        // Validate the values entered in the File object:
        if (count($errors) > 0) {

            $ok = false;

            // Return $errors in JSON format:
            $serializer = $this->get('jms_serializer');
            $serializer->serialize($errors, 'json');

            return array($errors, $ok);
        } // End of errors IF.
        // If no errors were found, handle the File data:
        $ok = true;
        $em = $this->getDoctrine()->getManager();

        $targetDir = 'campaign_files/' . $campaign->getId(); // Specify target directory for file.
        $file->upload($targetDir); // Move matrix file in target directory.

        $em->persist($file);
        $em->flush();

        return array($file->getUuid(), $ok);
    }

// End of moveFileAction() method.

    /**
     * Function to calculate the completeness of a campaign.
     * 
     * @param type $campaign
     * @return int
     */
    public function recalculate_campaign_completeness($campaign) {
        $completeness = 0;
        $tasks = $campaign->getTasks();

        //$em = $this->getDoctrine()->getManager();

        foreach ($tasks as $task) {
            $taskstatus = $task->getTaskstatus()->getName();
            if ($taskstatus == "Completed") {
                $completeness += 2;
            }
            if ($taskstatus == "Submitted") {
                $validated = $this->validate_task_requirements_are_met($task);
                if ($validated) {
                    $completeness += 1;
                }
            }
            if ($taskstatus == "Open") {
                $validated = $this->validate_task_requirements_are_met($task);
                if ($validated) {
                    $completeness += 1;
                }
            }
        }

        $campaign->setCompleteness($completeness);

        return $completeness;
    }

    function validate_task_requirements_are_met($task) {
        /////////////////////////////////////////////////
        //CHANGE THIS TO FALSEEE
        ////////////////////////////
        $is_validated = false;
        /////////////////////////////////
        /////////////////////////////////
        //$task = $this->getDoctrine()->getRepository('TaskBundle:Task')->find($task);
        $campaign = $task->getCampaign();

        switch ($task->getTaskname()->getName()) {
            case "JTBD":
                $campaign_start_date = $campaign->getStartdate();
                if ($campaign_start_date) {
                    $is_validated = true;
                }
                break;
            case "Comm Tasks":
//if ANY OF THE the Matrix Objective\<OBJECTIVE NAME>\Score > 0 , is validated.
                $lightdata_id = $campaign->getLightdata();
                if ($lightdata_id) {
                    $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
                    if ($lightdata) {
                        $objectives = $this->getDoctrine()->getRepository('LightdataBundle:ObjectiveLD')->findByLightdata($lightdata);

                        foreach ($objectives as $objective_value) {

                            $score = $objective_value->getScore();
                            if ($score > 0) {
                                $is_validated = true;
                            }
                        }
                    }
                }
                break;

            case "Real Lives":
// if the campaign has real lives url (not null) value , then is validated
                $reallivesurl = $campaign->getReallivesurl();
                if ($reallivesurl) {
                    $is_validated = true;
                }
                break;
            case "Media Idea":
// if there is a file uploadedd for this campaign with file_type_id = 15 , and attached to this task , then is validated
/// 
                $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(15);
                $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                        ->where('f.task = :task AND f.fileType = :fileType')
                        ->orderBy('f.updatedAt', 'DESC')
                        ->setMaxResults(1)
                        ->setParameter('task', $task)
                        ->setParameter('fileType', $fileType)
                        ->getQuery();
                $file = $fileQuery->getOneOrNullResult();

                if ($file) {
                    $is_validated = true;
                }

                break;
            case "Fundamental Channels":

//If ANY of the Matrix Touchpoints is selected , then is validated
                $lightdata_id = $campaign->getLightdata();
                if ($lightdata_id) {
                    $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
                    if ($lightdata) {
                        $touchpoints = $this->getDoctrine()->getRepository('LightdataBundle:TouchpointLD')->findByLightdata($lightdata);
                        foreach ($touchpoints as $touchpoint) {
                            $touchpoint_selected = $touchpoint->getSelected();
                            if ($touchpoint_selected) {
                                $is_validated = true;
                            }
                        }
                    }
                }

                break;
            case "Budget Allocation & Mapping":
// if Matrix has any allocated touchpoints populated under BudgetAllocation node, then add 1 point
                $lightdata_id = $campaign->getLightdata();
                if ($lightdata_id) {
                    $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
                    if ($lightdata) {
                        $budgetallocation = $this->getDoctrine()->getRepository('LightdataBundle:BudgetAllocationLD')->find($lightdata_id);
                        if ($budgetallocation) {
                            $total = $this->getDoctrine()->getRepository('LightdataBundle:BATotalLD')->findOneBy([
                                'budgetallocation' => $budgetallocation
                            ]);
                            if ($total) {
                                $allocation = $this->getDoctrine()->getRepository('LightdataBundle:BATOAllocationLD')->find($total->getId());
                                if ($allocation) {
                                    $grp = $allocation->getGRP();
                                    if ($grp > 0) {
                                        $is_validated = true;
                                    }
                                }
                            }
                        }
                    }
                }

                break;
            case "Phasing":
// old : If Matrix has any data under the TimeAllocation node, then add 1 point
// new : If any of TimeAllocation.Total.AllocationByPeriod[i].GRP  > 0     , is validated.           
                $lightdata_id = $campaign->getLightdata();
                if ($lightdata_id) {
                    $lightdata = $this->getDoctrine()->getRepository('LightdataBundle:Lightdata')->find($lightdata_id);
                    if ($lightdata) {
                        $timeallocation = $this->getDoctrine()->getRepository('LightdataBundle:TimeAllocationLD')->find($lightdata_id);
                        if ($timeallocation) {
                            $total = $this->getDoctrine()->getRepository('LightdataBundle:TATotalLD')->findOneBy([
                                'timeallocation' => $timeallocation
                            ]);
                            if ($total) {
                                $allocationsbyperiod = $this->getDoctrine()->getRepository('LightdataBundle:TATOAllocationByPeriod')->findBy(['allocatedtouchpoint' => $total]);
                                if ($allocationsbyperiod) {
                                    foreach ($allocationsbyperiod as $allocation) {
                                        if ($allocation->getGRP() > 0) {
                                            $is_validated = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            case "Final Plan":
//if there is a file uploaded for this campaign with file_type_id=12 and task_name_id=8, then add 1 point (check the project_file table)

                $fileType = $this->getDoctrine()->getRepository('CampaignBundle:Filetype')->findOneById(FileType::TYPE_WORKING_FLOW_CHART);

                $fileQuery = $this->getDoctrine()->getRepository('FileBundle:File')->createQueryBuilder('f')
                        ->where('f.task = :task AND f.fileType = :fileType')
                        ->orderBy('f.updatedAt', 'DESC')
                        ->setMaxResults(1)
                        ->setParameter('task', $task)
                        ->setParameter('fileType', $fileType)
                        ->getQuery();
                $file = $fileQuery->getOneOrNullResult();

                if ($file) {
                    $is_validated = true;
                }


                break;
            default:
                //echo "THIS ENTERED INTO DEFAULT MODE";
                break;
        }

        return $is_validated;
    }

}
