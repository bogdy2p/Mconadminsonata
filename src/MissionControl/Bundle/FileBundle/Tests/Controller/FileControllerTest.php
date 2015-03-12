<?php

namespace MissionControl\Bundle\FileBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileControllerTest extends WebTestCase {

	public $existing_campaign_id		= 'f29a70c2-2ea1-4dbc-bbf8-c4787e48092f';
	public $existing_api_key			= '8f45fa58-4664-b11b-82ee-7a737df7afb7';

	public function testFileManagement () {

		$client = static::createClient();

		// Create files:
		copy('web/uploads/test_data/resource.txt', 'web/uploads/test_data/fileManagementTest.txt');
		copy('web/uploads/test_data/resource.txt', 'web/uploads/test_data/fileManagementTest.docx');


		// Create uploaded files:
		$testFile1 = new UploadedFile(
			'web/uploads/test_data/fileManagementTest.txt',
			'fileManagementTest.txt',
			'text/plain',
			143
		);
		$testFile2 = new UploadedFile(
			'web/uploads/test_data/fileManagementTest.docx',
			'fileManagementTest.docx',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			146
 		);

		// Files to be uploaded:
		$datas = array(
			0 => array(
				'parameters' => array(
					'task_id' 		=> 1,
					'file_type_id' 	=> 1
				),
				'files' 	 => array(
					'campaign_file' => $testFile1
				),
				'server'	 => array(
					'HTTP_x-wsse' => 'ApiKey="' . $this->existing_api_key . '"'
				)
			),
			1 => array(
				'parameters' => array(
					'task_id' 		=> 1,
					'file_type_id' 	=> 1
				),
				'files' 	 => array(
					'campaign_file' => $testFile1
				),
				'server'	 => array(
					'HTTP_x-wsse' => 'ApiKey="' . $this->existing_api_key . '"'
				)
			),
			2 => array(
				'parameters' => array(
					'task_id' 		=> 1,
					'file_type_id' 	=> 2
				),
				'files' 	 => array(
					'campaign_file' => $testFile2
				),
				'server'	 => array(
					'HTTP_x-wsse' => 'ApiKey="' . $this->existing_api_key . '"'
				)
			),
			3 => array(
				'parameters' => array(
					'task_id' 		=> 2,
					'file_type_id' 	=> 1
				),
				'files' 	 => array(
					'campaign_file' => $testFile1
				),
				'server'	 => array(
					'HTTP_x-wsse' => 'ApiKey="' . $this->existing_api_key . '"'
				)
			),
		);

		// Upload file sequence:
		foreach ($datas as $data) { 

			// $client->insulate();
			$client->request(
				'POST', 
				'api/v1/campaigns/' . $this->existing_campaign_id . '/files',
				$data['parameters'],
				$data['files'],
				$data['server']
			);

			$this->assertEquals(200, $client->getResponse()->getStatusCode());

			// Restart client and prepare file for next upload:
			$client->restart();
			copy('web/uploads/test_data/resource.txt', 'web/uploads/test_data/fileManagementTest.txt');

		}


		// Test GET /campaign/{campaignId}/files functionality:
		$client->request(
			'GET',
			'api/v1/campaigns/' . $this->existing_campaign_id . '/files',
			array(),
			array(),
			array(
				'HTTP_x-wsse' => 'ApiKey="' . $this->existing_api_key . '"'
			)
		);

		$files = json_decode($client->getResponse()->getContent(), TRUE); // Array of available files for campaign.

		$this->assertEquals(2, $files['files'][0]['FileVersion']);
		$this->assertEquals(1, $files['files'][1]['FileVersion']);
		$this->assertEquals(1, $files['files'][2]['FileVersion']);

	
		// Test PUT /files/{fileId}
		$client->restart();
		$client->request(
			'PUT',
			'api/v1/files/' . $files['files'][0]['FileId'],
			array(),
			array(),
			array(
				'HTTP_x-wsse' => 'ApiKey="' . $this->existing_api_key . '"'
			)
		);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertContains('Request completed. File not_visible set to TRUE.', $client->getResponse()->getContent());


		// Test file versioning:
		$client->restart();
		$client->request(
			'GET',
			'api/v1/campaigns/' . $this->existing_campaign_id . '/files',
			array(),
			array(),
			array(
				'HTTP_x-wsse' => 'ApiKey="' . $this->existing_api_key . '"'
			)
		);
		$files = json_decode($client->getResponse()->getContent(), TRUE); // Array of available files for campaign.
		$this->assertEquals(1, $files['files'][0]['FileVersion']);

		// Re-upload file to check versioning logic:
		$client->restart();
		$client->request(
			'POST', 
			'api/v1/campaigns/' . $this->existing_campaign_id . '/files',
			$datas[0]['parameters'],
			$datas[0]['files'],
			$datas[0]['server']
		);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());

		$client->restart();
		$client->request(
			'GET',
			'api/v1/campaigns/' . $this->existing_campaign_id . '/files',
			array(),
			array(),
			array(
				'HTTP_x-wsse' => 'ApiKey="' . $this->existing_api_key . '"'
			)
		);

		$files = json_decode($client->getResponse()->getContent(), TRUE); // Array of available files for campaign.
	
		$this->assertEquals(2, $files['files'][0]['FileVersion']);

	}

}