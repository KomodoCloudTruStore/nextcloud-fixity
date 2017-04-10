<?php
namespace OCA\Fixity\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

class FixityController extends Controller {
	private $userId;

	public function __construct($AppName, IRequest $request, $UserId){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
	}


	/**
	 * callback function to get md5 hash of a file
	 * @NoAdminRequired
	 * @param (string) $source - filename
	 * @param (string) $type - hash algorithm type
	 */
	  public function check($source, $type) {
  		if(!$this->checkAlgorithmType($type)) {
  			return new JSONResponse(
				array(
					'response' => 'error',
					'msg' => $this->language->t('The algorithm type "%s" is not a valid or supported algorithm type.', array($type))
				)
			);
		}

		if($hash = $this->getHash($source, $type)){
			return new JSONResponse(
				array(
					'response' => 'success',
					'msg' => $hash
				)
			);
		} else {
			return new JSONResponse(
				array(
					'response' => 'error',
					'msg' => $this->language->t('File not found.')
				)
			);
		};
	  }

	  protected function getHash($source, $type) {
	  	if($info = Filesystem::getLocalFile($source)) {
			return hash_file($type, $info);
	  	}
	  	return false;
	  }

	  protected function checkAlgorithmType($type) {
	  	$list_algos = hash_algos();
	  	return in_array($type, $this->getAllowedAlgorithmTypes()) && in_array($type, $list_algos);
	  }

	  protected function getAllowedAlgorithmTypes() {
	  	return array('md5', 'sha256');
	}
}

