<?php

namespace OCA\Fixity\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\Fixity\Service\FixityService;
use OCP\IUserSession;

class FixityController extends Controller {

    use Errors;

    private $service;
    private $userId;

    public function __construct($AppName, IRequest $request, FixityService $service, IUserSession $session) {
        parent::__construct($AppName, $request);

        $this->service = $service;
		$this->userId = $session->getUser()->getUID();

    }

    /**
     * @NoAdminRequired
     */
    public function index($id) {
        return new DataResponse($this->service->findAll($id));
    }

    /**
     * @NoAdminRequired
     *
     * @param int $file_id
     */
    public function show($id) {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->show($id);
        });
    }

    /**
     * @NoAdminRequired
     *
     * @param string $title
     * @param string $content
     */
    public function create($file_id, $type) {

        return $this->service->create($file_id, $type, $this->userId);
    }

    public function validate($id) {

        $valid = $this->service->validate($id, $this->userId);

        return $valid;
    }


}