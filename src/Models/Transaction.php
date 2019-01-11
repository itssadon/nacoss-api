<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
  protected $table = 'transactions';
  public $primaryKey = 'transaction_ref';
  public $timestamp = false;
  public $incrementing = false;
  protected $fillable = [
    'transaction_ref',
    'email',
    'phone',
    'amount',
    'response_code',
    'response_message',
    'purpose_id'
  ];

  public function getPayload() {
    return [
      'transactionRef' => $this->transaction_ref,
      'email' => $this->email,
      'phone' => $this->phone,
      'amount' => $this->amount,
      'responseCode' => $this->response_code,
      'responseMessage' => $this->response_message
    ];
  }
}