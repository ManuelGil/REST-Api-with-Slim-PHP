<?php

require '../vendor/autoload.php';

$app = new \Slim\App;

require '../src/routes/api.php';

$app->run();

?>
