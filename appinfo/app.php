<?php

use OCP\Util;

$eventDispatcher = \OC::$server->getEventDispatcher();

$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
	Util::addScript('fixity', 'fixity.tabview' );
	Util::addScript('fixity', 'fixity.plugin' );
});
