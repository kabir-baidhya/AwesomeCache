<?php
/*
 * This file is part of the AwesomeCache package.
 *
 * (c) Kabir Baidhya <kabeer182010@gmail.com>
 *
 */

error_reporting(E_ALL);

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->addPsr4('Gckabir\\AwesomeCache\\', __DIR__.'/AwesomeCache');
