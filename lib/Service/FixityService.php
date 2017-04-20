<?php
namespace OCA\Fixity\Service;

use Exception;

use OCA\Fixity\Storage\FixityStorage;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;

use OCA\Fixity\Db\FixityHash;
use OCA\Fixity\Db\FixityHashMapper;


class FixityService {

    private $mapper;
    private $storage;

    public function __construct(FixityHashMapper $mapper, FixityStorage $storage){
        $this->mapper = $mapper;
        $this->storage = $storage;;
    }

    private function handleException ($e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new FixityNotFoundException($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function validate($id) {

        $valid = true;

        foreach ($this->show($id) as $hash) {

            if ($hash->getHash() != $this->storage->getHash($hash->getFileId(), $hash->getType())) {

                $valid = false;

            }

        }

        return $valid;

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