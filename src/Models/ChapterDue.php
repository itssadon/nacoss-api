<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class ChapterDue extends Model {
  protected $table = 'chapter_dues';
  public $primaryKey = 'chapter_name';
  public $timestamp = true;
  public $incrementing = false;
  protected $fillable = [
    'chapter_name',
    'transaction_ref'
  ];

  public function getPayload() {
    return [
      'chapter_name' => $this->chapter_name,
      'transactionRef' => $this->transaction_ref,
      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at
    ];
  }
}