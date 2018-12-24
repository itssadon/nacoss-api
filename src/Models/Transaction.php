<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
  protected $table = 'transactions';
  public $timestamp = false;
  public $incrementing = true;
  protected $fillable = [
    'email',
    'phone',
    'transaction_ref',
    'amount',
    'response_code',
    'response_message',
    'purpose_id'
  ];

  public function getPayload() {
    return [
      'email' => $this->email,
      'phone' => $this->phone,
      'transactionRef' => $this->transaction_ref,
      'amount' => $this->amount,
      'responseCode' => $this->response_code,
      'responseMessage' => $this->response_message
    ];
  }
}