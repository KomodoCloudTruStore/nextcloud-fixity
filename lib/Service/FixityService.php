<?php
namespace OCA\Fixity\Service;

use Exception;

use OCA\Fixity\Storage\FixityStorage;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Activity\IManager;

use OCA\Fixity\Db\FixityHash;
use OCA\Fixity\Db\FixityHashMapper;


class FixityService {

    private $mapper;
    private $storage;
	private $server;
    private $activityManager;

    public function __construct(IManager $activity, FixityHashMapper $mapper, FixityStorage  $storage) {
        $this->mapper = $mapper;
        $this->storage = $storage;
        $this->activityManager = $activity;

    }

    private function handleException ($e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new FixityServiceNotFoundException($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function validate($id, $userId) {

        $valid = true;

        foreach ($this->show($id) as $hash) {

            if ($hash->getHash() != $this->storage->getHash($hash->getFileId(), $hash->getType())) {

                $valid = false;

            }

        }

		$this->createEvent($userId, 'validate_fixity_subject', $id);

        return $valid;

    }

    public function createEvent($userId, $subject, $fileId)
    {

		$path = $this->storage->getNode($fileId)->getInternalPath();

    	$event = $this->activityManager->generateEvent();

        $event->setApp('fixity');
        $event->setType('fixity');
        $event->setAffectedUser($userId);
        $event->setAuthor($userId);
        $event->setTimestamp(time());
		$event->setSubject($subject, [$userId, $path]);
		$event->setObject('files', (int)$fileId);

        $this->activityManager->publish($event);

    }

    public function show($id) {

        try {
            return  $this->mapper->findAll($id);
        } catch(Exception $e) {
            $this->handleException($e);
        }

    }

    public function create($id, $type, $userId) {

        $hash = new FixityHash();

        $hash->setFileId($id);
        $hash->setType($type);
        $hash->setHash($this->storage->getHash($id, $type));
        $hash->setTimestamp(date("Y-m-d H:i:s"));

        $createdHash = $this->mapper->insert($hash);

        $this->createEvent($userId, 'create_fixity_subject', $id);

        return $createdHash;
    }

}