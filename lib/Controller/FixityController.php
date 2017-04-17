<?php

namespace OCA\Fixity\Controller;

use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;

use OCA\Fixity\Service\FixityService;

class FixityController extends Controller {

    use Errors;

    private $service;

    public function __construct($AppName, IRequest $request, FixityService $service) {
        parent::__construct($AppName, $request);

        $this->service = $service;

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
    public function show($file_id) {
        return $this->handleNotFound(function () use ($file_id) {
            return $this->service->show($file_id);
        });
    }

    /**
     * @NoAdminRequired
     *
     * @param string $title
     * @param string $content
     */
    public function create($file_id, $type) {
        return $this->service->create($file_id, $type);
    }


}