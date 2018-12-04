<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model {
  protected $table = 'gender';
  public $primaryKey = 'gender_id';
  public $timestamp = false;
  public $incrementing = false;
  protected $fillable = [
    'gender_id',
    'gender_name'
  ];

  public function getPayload() {
    return [
      'genderId' => $this->gender_id,
      'genderName' => $this->gender_name
    ];
  }
}