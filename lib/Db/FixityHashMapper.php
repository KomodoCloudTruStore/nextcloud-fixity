<?php

namespace OCA\Fixity\Db;

use OCP\IDb;
use OCP\AppFramework\Db\Mapper;

class FixityHashMapper extends Mapper {

    public function __construct(IDb $db) {
        parent::__construct($db, 'fixity_hashes', '\OCA\Fixity\Db\FixityHash');
    }


    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function find($id) {
        $sql = 'SELECT * FROM `*PREFIX*fixity_hashes` ' .
            'WHERE `id` = ?';
        return $this->findEntity($sql, [$id]);
    }

    public function findAll($file_id) {
        $sql = 'SELECT * FROM `*PREFIX*fixity_hashes`';

        return $this->findEntities($sql);
    }



}