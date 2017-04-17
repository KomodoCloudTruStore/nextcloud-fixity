<?php

namespace OCA\Fixity\AppInfo;


use \OCP\AppFramework\App;

use \OCA\Fixity\Controller\FixityController;
use \OCA\Fixity\Service\FixityService;
use \OCA\Fixity\Storage\FixityStorage;
use \OCA\Fixity\Db\FixityHashMapper;
use \OCA\Fixity\Db\FixityHashDAO;

class Application extends App {

    public function __construct(array $urlParams=array())
    {
        parent::__construct('fixity', $urlParams);

        $container = $this->getContainer();

        /*
         * Controllers
         */

        $container->registerService('FixityController', function($c){
            return new FixityController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('FixityService')
            );
        });

        /**
         * Services
         */
        $container->registerService('FixityService', function($c){
            return new FixityService(
                $c->query('FixityHashMapper'),
                $c->query('FixityStorage'),
                $c->query('FixityHashDAO')
            );
        });

        /**
         * Mappers
         */
        $container->registerService('FixityHashMapper', function($c){
            return new FixityHashMapper(
                $c->query('ServerContainer')->getDb()
            );
        });

        $container->registerService('FixityHashDAO', function($c){
            return new FixityHashDAO(
                $c->query('ServerContainer')->getDb()
            );
        });

        /**
         * Storage
         */
        $container->registerService('FixityStorage', function($c){
            return new FixityStorage(
                $c->query('ServerContainer')->getRootFolder()
            );
        });

    }

}