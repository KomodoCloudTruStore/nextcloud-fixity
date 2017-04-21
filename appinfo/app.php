<?php
use OCP\AppFramework\App;
use OCP\Util;

$app = new App('fixity');
$container = $app->getContainer();

$eventDispatcher = \OC::$server->getEventDispatcher();
$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
    Util::addScript('fixity', 'fixity.tabview' );
    Util::addScript('fixity', 'fixity.plugin' );
});