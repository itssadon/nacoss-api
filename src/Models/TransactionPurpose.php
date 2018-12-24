<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionPurpose extends Model {
  protected $table = 'transaction_purposes';
  public $primaryKey = 'purpose_id';
  public $timestamp = false;
  public $incrementing = false;
  protected $fillable = [
    'purpose_id',
    'purpose_description',
    'amount'
  ];

  public function getPayload() {
    return [
      'purpose_id' => $this->purpose_id,
      'purpose_description' => $this->purpose_description,
      'amount' => $this->amount
    ];
  }
}