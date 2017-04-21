<?php
namespace OCA\Fixity\Hooks;

use OCP\Files\IRootFolder;

class FileHooks {

    protected $root;

    public function __construct(IRootFolder $root){
        $this->root = $root;
    }

    public function register() {

        $callbackCreate = function ($node) {

            return true;

        };

        $callbackWrite = function ($node) {

            return true;

        };
        $this->root->listen('\OC\Files', 'postCreate', $callbackCreate);
        $this->root->listen('\OC\Files', 'postWrite', $callbackWrite);
    }

}