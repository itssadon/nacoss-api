<?php

use NACOSS\Controllers\ChapterController;

$this->get('', ChapterController::class . ':getAllChapters');
$this->get('/search/{search-term}', ChapterController::class . ':searchChapter');

$this->post('', ChapterController::class . ':addChapter');
$this->post('/complete', ChapterController::class . ':completeChapterRegistration');

$this->get('/active', ChapterController::class . ':getActiveChapters');
$this->get('/inactive', ChapterController::class . ':getInActiveChapters');

$this->post('/activate', ChapterController::class . ':updateChapterDue');