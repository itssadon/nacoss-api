<?php

use NACOSS\Controllers\Lookups\GenderController;
use NACOSS\Controllers\Lookups\ZoneController;

$this->get('/gender', GenderController::class . ':getAllGender');
$this->get('/zones', ZoneController::class . ':getAllZones');