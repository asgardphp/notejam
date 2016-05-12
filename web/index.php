<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

require_once '../autoload.php'; #composer autoloader

/* RUN & SEND */
$kernel = new Kernel();
$kernel->load();
$kernel->getContainer()['httpKernel']->run()->send();