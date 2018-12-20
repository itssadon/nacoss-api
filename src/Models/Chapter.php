<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model {
  protected $table = 'chapters';
  public $primaryKey = 'school_alias';
  public $timestamp = true;
  public $incrementing = false;
  protected $fillable = [
    'school_alias',
    'school_name',
    'chapter_name',
    'zone_id',
    'chapter_reg_num',
    'chapter_email',
    'address',
    'hod_name',
    'hod_phone',
    'slogan',
    'logo'
  ];

  public function getPayload($chapter) {
    return [
      'schoolAlias' => $chapter->school_alias,
      'schoolName' => $chapter->school_name,
      'chapterName' => $chapter->chapter_name,
      'chapterRegistrationNumber' => $chapter->chapter_reg_num,
      'zone' => $chapter->zone_name,
      'chapterEmail' => $chapter->chapter_email,
      'address' => $chapter->address,
      'hodName' => $chapter->hod_name,
      'hodPhone' => $chapter->hod_phone
    ];
  }
}