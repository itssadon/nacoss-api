<?php

use NACOSS\Controllers\ChapterController;

$this->post('', ChapterController::class . ':addChapter');
$this->put('', ChapterController::class . ':updateChapterRegistration');
$this->get('/search/{search-term}', ChapterController::class . ':searchChapter');
$this->get('', ChapterController::class . ':getAllChapters');
$this->get('/active', ChapterController::class . ':getActiveChapters');
$this->post('/activate', ChapterController::class . ':updateChapterDue');