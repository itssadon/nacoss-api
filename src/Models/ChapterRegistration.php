<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterRegistration extends Model {
  protected $table = 'chapter_registrations';
  public $primaryKey = 'chapter_name';
  public $timestamp = false;
  public $incrementing = true;
  protected $fillable = [
    'chapter_name',
    'transaction_ref'
  ];

  public function getPayload() {
    return [
      'chapterName' => $this->chapter_name,
      'transactionRef' => $this->transaction_ref,
      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at
    ];
  }
}