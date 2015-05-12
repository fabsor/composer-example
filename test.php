<?php
require_once('vendor/autoload.php');

use Fabsor\ExampleCommand;

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new ExampleCommand());
$application->run();
