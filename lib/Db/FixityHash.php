<?php

namespace OCA\Fixity\Db;

use OCP\AppFramework\Db\Entity;
use JsonSerializable;

class FixityHash extends Entity implements JsonSerializable {

    protected $fileId;
    protected $type;
    protected $hash;
    protected $timestamp;

    public function jsonSerialize() {
        return [
            'type' => $this->type,
            'hash' => $this->hash,
            'timestamp' => $this->timestamp
        ];
    }
}