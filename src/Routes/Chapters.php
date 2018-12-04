<?php

use NACOSS\Controllers\ChapterController;

$this->post('', ChapterController::class . ':addChapter');
$this->get('', ChapterController::class . ':getAllChapters');
$this->get('/active', ChapterController::class . ':getActiveChapters');
$this->post('/activate', ChapterController::class . ':updateChapterDue');