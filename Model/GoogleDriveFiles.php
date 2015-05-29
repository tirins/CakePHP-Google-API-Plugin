<?php
App::uses('GoogleApi', 'Google.Model');
class GoogleDriveFiles extends GoogleApi {

	public $hasOne = array('Google.GoogleDriveFilesUpload');

	protected $_request = array(
		'method' => 'GET',
		'uri' => array(
			'scheme' => 'https',
			'host' => 'www.googleapis.com',
			'path' => '/drive/v2/files',
		)
	);
	
	/**
	 * https://developers.google.com/drive/v2/reference/files/delete
	 **/
	public function deleteFile($id, $options = array()) {
		$request = array();
		$request['uri']['query'] = $options;
		$request['method'] = 'DELETE';
		return $this->_request(sprintf('/%s', $id), $request);
	}
	
	/**
	 * https://developers.google.com/drive/v2/reference/files/get
	 **/
	public function getFile($id, $options = array()) {
		$request = array();
		$request['uri']['query'] = $options;
		return $this->_request(sprintf('/%s', $id), $request);
	}

	/**
	 * https://developers.google.com/drive/v2/reference/files/insert
	 * $parentFolders array of parent folder ids
	 **/
	public function insertFile($file, $parentFolders = array(), $options = array()) {
		if(!empty($parentFolders)){
			$parentFolders = Hash::map($parentFolders, '{n}', function($el){
				return array(
						'id' => $el, 
						'kind' => 'drive#fileLink'
					);
				
			});
		}

		$request = array();
		$request['method'] = 'POST';
		$body = array(
			'title' => $file['name'],
			'mimeType' => $file['type'],
			'parents' => $parentFolders
		);
		$request['body'] = json_encode($body);
		//debug($request['body']);
		$request['header']['Content-Type'] = 'application/json';
		return $this->GoogleDriveFilesUpload->insertFile(
			$file, $this->_request(null, $request),
			$options
		);
	}

	/**
	 * https://developers.google.com/drive/v2/reference/files/list
	 **/
	public function listItems($options = array()) {
		$request = array();
		if ($options) {
			$request['uri']['query'] = $options;
		}
		return $this->_request(null, $request);
	}

	public function insertFolder($foldername, $parentFolders = array(), $options = array()){
		$request = array();
		$request['method'] = 'POST';
		$foldermime = 'application/vnd.google-apps.folder';
		$body = array(
			'title' => $foldername,
			'mimeType' => $foldermime,
			'parents' => array($parentFolders)
		);
		$request['body'] = json_encode($body);
		//debug($request['body']);
		$request['header']['Content-Type'] = 'application/json';
		
		if ($options) {
			$request['uri']['query'] = $options;
		}
		
		return $this->_request(null, $request);
	}
}
