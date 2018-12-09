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
    'slogan',
    'logo'
  ];

  public function getPayload() {
    return [
      'schoolAlias' => $this->school_alias,
      'schoolName' => $this->school_name,
      'chapterName' => $this->chapter_name,
      'zoneId' => $this->zone_id,
      'chapterRegistrationNumber' => $this->chapter_reg_num,
      'chapterEmail' => $this->chapter_email,
      'address' => $this->address,
      'slogan' => $this->slogan,
      'logo' => $this->logo
    ];
  }
}