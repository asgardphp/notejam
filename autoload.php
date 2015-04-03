<?php
$composer = require 'vendor/autoload.php';

$composer->setUseIncludePath(true);
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__.'/app');