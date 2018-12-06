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
    'is_genuine',
    'created_at'
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
      'isGenuine' => $this->is_genuine,
      'createdAt' => $this->created_at
    ];
  }

  public function getFullPayload($member) {
    return [
      'mrn'=> (string) $member->mrn,
      'surname'=> $member->surname,
      'firstName'=> $member->firstname,
      'otherName'=> $member->othername,
      'genderId'=> $member->gender_id,
      'phone'=> $member->phone,
      'email'=> $member->email,
      'dateOfBirth'=> $member->date_of_birth,
      'photo'=> $member->photo,
      'twitter'=> $member->twitter,
      'facebook'=> $member->facebook,
      'linkedin'=> $member->linkedin,
      'website'=> $member->website,
      'schoolAlias'=> $member->school_alias,
      'skills'=> $member->skills,
      'issuedCert'=> $member->issued_cert,
      'isGenuine'=> $member->is_genuine,
      'createdAt'=> $member->created_at,
      'updatedAt'=> $member->updated_at
    ];
  }

}