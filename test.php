<?php
require_once('vendor/autoload.php');

use Fabsor\LsCommand;
use Fabsor\TestCommand;
use Fabsor\OrmCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new TestCommand());
$application->add(new LsCommand());
$application->add(new OrmCommand());
$application->run();
