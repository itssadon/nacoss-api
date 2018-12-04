<?php

use NACOSS\Controllers\MemberController;

$this->post('', MemberController::class . ':addMember');
$this->get('', MemberController::class . ':getAllMembers');
$this->get('/{mrn}', MemberController::class . ':getMemberDetails');