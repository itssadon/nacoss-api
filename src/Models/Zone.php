<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model {
  protected $table = 'zones';
  public $primaryKey = 'zone_id';
  public $timestamp = false;
  public $incrementing = false;
  protected $fillable = [
    'zone_id',
    'zone_name'
  ];

  public function getPayload() {
    return [
      'zoneId' => $this->zone_id,
      'zoneName' => $this->zone_name
    ];
  }
}