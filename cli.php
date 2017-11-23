<?php

declare(strict_types=1);
require __DIR__.'/vendor/autoload.php';

$endpoint = $argv[1];
$startTime = $argv[2];
$endTime = $argv[3];
$numberOfTravelers = (int) $argv[4];

$service = \GetYourGuide\GetYourGuide::factoryWithEndpoint($endpoint);
echo json_encode($service->getList($startTime, $endTime, $numberOfTravelers), JSON_PRETTY_PRINT);
