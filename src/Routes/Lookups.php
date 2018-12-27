<?php

use NACOSS\Controllers\Lookups\GenderController;

$this->get('/gender', GenderController::class . ':getAllGender');