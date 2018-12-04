<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterDue extends Model {
  protected $table = 'chapter_dues';
  public $timestamp = false;
  public $incrementing = true;
  protected $fillable = [
    'mrn',
    'surname',
    'firstname',
    'othername',
    'gender_id',
    'phone',
    'date_of_birth',
    'photo',
    'twitter',
    'facebook',
    'linkedin',
    'website'
  ];

  public function getPayload() {
    return [
      'mrn' => $this->mrn,
      'surname' => $this->surname,
      'firstName' => $this->firstname,
      'otherName' => $this->othername,
      'genderId' => $this->gender_id,
      'phone' => $this->phone,
      'dateOfBirth' => $this->date_of_birth,
      'photo' => $this->photo,
      'twitter' => $this->twitter,
      'facebook' => $this->facebook,
      'linkedin' => $this->linkedin,
      'website' => $this->website
    ];
  }
}