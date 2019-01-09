<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class WelfareScheme extends Model {
  protected $table = 'welfare_scheme';
  public $primaryKey = ['mrn', 'cover_year'];
  public $timestamp = false;
  public $incrementing = false;
  protected $fillable = [
    'mrn',
    'cover_year',
    'beneficiary_name',
    'beneficiary_phone'
  ];
  
  protected $hidden = [
    'password'
  ];

  public function getPayload() {
    return [
      'mrn' => $this->mrn,
      'coverYear' => $this->cover_year,
      'beneficiary_name' => $this->beneficiary_name,
      'beneficiary_phone' => $this->beneficiary_phone
    ];
  }

}