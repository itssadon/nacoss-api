<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterRegistration extends Model {
  protected $table = 'chapter_registrations';
  public $timestamp = false;
  public $incrementing = true;
  protected $fillable = [
    'chapter_name', 'transaction_ref', 'created_at'
  ];

  public function getPayload() {
    return [
      'chapterName' => $this->chapter_name,
      'transactionRef' => $this->transaction_ref,
      'createdAt' => $this->created_at
    ];
  }
}