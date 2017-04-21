<?php
namespace OCA\Fixity\Storage;

class FixityStorage {

    private $storage;

    public function __construct($storage){
        $this->storage = $storage;
    }

    public function getNode($id) {
        try {
            return $this->storage->getById($id)[0];
        } catch(\OCP\Files\NotFoundException $e) {
            throw new FixityStorageException('Node does not exist');
        }
    }

    public function getHash($id, $type) {

        if ($type === "md5") {

            return $this->getMD5($id);

        } elseif ($type === "sha256") {

            return $this->getSHA256($id);

        } else {

            throw new FixityStorageException('Unsupported Algorithm');

        }


    }

    public function getMD5($id) {

        $node = $this->getNode($id);

        return $node->getStorage()->hash('md5', $node->getInternalPath());

    }

    public function getSHA256($id) {

        $node = $this->getNode($id);

        return $node->getStorage()->hash('sha256', $node->getInternalPath());

    }
}