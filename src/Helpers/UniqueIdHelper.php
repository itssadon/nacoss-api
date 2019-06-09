<?php
namespace NACOSS\Helpers;

use NACOSS\Helpers\UniqueIdHelper;
use NACOSS\Models\Member;

class UniqueIdHelper {
  public function getUniqueId(Int $len) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVXYZ';
    $uniqueId = '';
    for ($i = 0; $i < $len; $i++) {
      $randomInt = rand(0, strlen($characters) - 1);
      $uniqueId .= $characters[$randomInt];
    }
    return $uniqueId;
  }

  public function generateChapterRegNum($alias) {
    $time = substr(time(), 3, 5);
    $school_alias = substr($alias, 0, 2);
    $year = date('Y');
    $numberTemp = "NA".$year.$school_alias.$time;
    return $numberTemp;
  }

  public function generateNacossId() {
    $year = substr(date('Y'), 2, 2);
    $nacoss_id = $year.substr(str_shuffle('0123456780ABCDEFGHJKMNPQRSTUVWXYZ'), 0, 8).'NA';
    while ($this->dataExists($nacoss_id)) {
      $nacoss_id = $year.substr(str_shuffle('0123456780ABCDEFGHJKMNPQRSTUVWXYZ'), 0, 8).'NA';
    }
    return $nacoss_id;
  }

  public function dataExists(string $data) {
    return Member::where('mrn', $data)->exists();
  }

}