<?php

$version = exec('git rev-parse HEAD');
$container->setParameter('app_version', substr($version, 0, 7));
