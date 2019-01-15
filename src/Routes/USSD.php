<?php

use NACOSS\Controllers\Messaging\USSDController;

$this->post('', USSDController::class . ':executeUSSDOperation');