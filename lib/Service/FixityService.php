<?php
namespace OCA\Fixity\Service;

use Exception;

use OCA\Fixity\Storage\FixityStorage;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\Activity\IManager;
use OCP\IUserSession;
use OCP\IUser;

use OCA\Fixity\Db\FixityHash;
use OCA\Fixity\Db\FixityHashMapper;


class FixityService {

    private $mapper;
    private $storage;
    private $activityManager;
    protected $session;

    public function __construct(IManager $activity, IUserSession $session, FixityHashMapper $mapper, FixityStorage $storage){
        $this->mapper = $mapper;
        $this->storage = $storage;
        $this->activityManager = $activity;
        $this->session = $session;

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

    public function createEvent($hashId)
    {

        $actor = $this->session->getUser();
        if ($actor instanceof IUser) {
            $actor = $actor->getUID();
        } else {
            $actor = '';
        }

        $event = $this->activityManager->generateEvent();

        $event->setApp('fixity');
        $event->setType('fixity_hashes');
        $event->setAffectedUser($actor);
        $event->setAuthor($actor);
        $event->setTimestamp(time());
        $event->setSubject('Created new Fixity Hash');
        $event->setObject('fixity',$hashId);

        $this->activityManager->publish($event);

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

        $createdHash = $this->mapper->insert($hash);

        $this->createEvent($createdHash->getId());

        return $createdHash;
    }

}