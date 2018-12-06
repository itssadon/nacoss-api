<?php

use NACOSS\Controllers\TransactionController;

$this->post('', TransactionController::class . ':logTransaction');
$this->get('', TransactionController::class . ':getAllTransactions');