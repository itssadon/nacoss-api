<?php

use NACOSS\Controllers\MemberController;

$this->post('', MemberController::class . ':addMember');
$this->get('', MemberController::class . ':getAllMembers');
$this->get('/uncovered', MemberController::class . ':getUncoveredMembers');
$this->post('/cover', MemberController::class . ':insureMember');
$this->get('/search', MemberController::class . ':searchMember');
$this->get('/{mrn}', MemberController::class . ':getMemberDetails');
$this->put('/{mrn}', MemberController::class . ':updateMemberDetails');