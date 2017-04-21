<?php


namespace OCA\Fixity\Db;

use OCP\IDBConnection;

class FixityHashDAO {

    private $db;

    public function __construct(IDBConnection $db) {
        $this->db = $db;
    }

    public function findByFileId($id) {
        $sql = 'SELECT * FROM `*PREFIX*fixity_hashes` ' .
            'WHERE `file_id` = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $id, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetch();

        $stmt->closeCursor();
        return $rows;
    }

}