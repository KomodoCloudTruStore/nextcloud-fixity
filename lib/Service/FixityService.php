<?php
namespace OCA\Fixity\Service;

use Exception;

use OCA\Fixity\Storage\FixityStorage;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Fixity\Db\FixityHash;
use OCA\Fixity\Db\FixityHashMapper;
use OCA\Fixity\Db\FixityHashDAO;


class FixityService {

    private $mapper;
    private $storage;
    private $db;

    public function __construct(FixityHashMapper $mapper, FixityStorage $storage, FixityHashDAO $db){
        $this->mapper = $mapper;
        $this->storage = $storage;
        $this->db = $db;
    }

    private function handleException ($e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new FixityNotFoundException($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function show($id) {

        try {
            return  $this->mapper->findAll($id);
        } catch(Exception $e) {
            $this->handleException($e);
        }

    }

    public function create($id, $type) {

        $hash = new FixityHash();

        $hash->setFileId($id);
        $hash->setType($type);
        $hash->setHash($this->storage->getHash($id, $type));
        $hash->setTimestamp(date("Y-m-d H:i:s"));

        return $this->mapper->insert($hash);
    }

}