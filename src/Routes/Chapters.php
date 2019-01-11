<?php

use NACOSS\Controllers\ChapterController;

$this->post('', ChapterController::class . ':addChapter');
$this->post('/complete', ChapterController::class . ':updateChapterRegistration');
$this->get('/search/{search-term}', ChapterController::class . ':searchChapter');
$this->get('', ChapterController::class . ':getAllChapters');
$this->get('/active', ChapterController::class . ':getActiveChapters');
$this->get('/inactive', ChapterController::class . ':getInActiveChapters');
$this->post('/activate', ChapterController::class . ':updateChapterDue');