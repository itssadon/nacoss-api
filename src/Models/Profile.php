<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model {
  protected $table = 'profiles';
  public $primaryKey = 'mrn';
  public $timestamp = true;
  public $incrementing = false;
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
      'surname' => $this->surname,
      'firstname' => $this->firstname,
      'othername' => $this->othername,
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