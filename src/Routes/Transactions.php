<?php

use NACOSS\Controllers\TransactionController;

$this->post('', TransactionController::class . ':addTransaction');
$this->get('', TransactionController::class . ':getAllTransactions');