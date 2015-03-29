<?php

use ZCode\Lighting\Application;

session_start();

require 'vendor/autoload.php';

$app = new Application();
$app->render();
