<?php
namespace NACOSS\Models;

use NACOSS\Models\Profile;
use Illuminate\Database\Eloquent\Model;

class Member extends Model {
  protected $table = 'members';
  public $primaryKey = 'mrn';
  public $timestamp = false;
  public $incrementing = false;
  protected $fillable = [
    'mrn',
    'school_alias',
    'skills',
    'issued_cert',
    'is_genuine'
  ];
  
  protected $hidden = [
    'password'
  ];

  public function getPayload() {
    return [
      'mrn' => $this->mrn,
      'schoolAlias' => $this->school_alias,
      'skills' => $this->skills,
      'issuedCert' => $this->issued_cert,
      'isGenuine' => $this->is_genuine
    ];
  }

  public function getFullPayload($member) {
    return [
      'mrn'=> $member->mrn,
      'surname'=> $member->surname,
      'firstName'=> $member->firstname,
      'otherName'=> $member->othername,
      'genderId'=> $member->gender_id,
      'phone'=> $member->phone,
      'email'=> $member->email,
      'dateOfBirth'=> $member->date_of_birth,
      'photo'=> $member->photo,
      'schoolAlias'=> $member->school_alias,
      'twitter'=> $member->twitter,
      'facebook'=> $member->facebook,
      'linkedin'=> $member->linkedin,
      'website'=> $member->website,
      'skills'=> $member->skills,
      'issuedCert'=> $member->issued_cert,
      'isGenuine'=> $member->is_genuine
    ];
  }

}