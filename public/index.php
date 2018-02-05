<?php
chdir(dirname(__DIR__));

require 'vendor/autoload.php';

$app = new \Core\Application();
$app->run();