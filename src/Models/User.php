<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model {
  protected $table = 'users';
  public $primaryKey = 'mrn';
  public $timestamp = false;
  public $incrementing = false;
  protected $fillable = [
    'mrn',
    'email',
    'password',
    'user_role',
    'created_at'
  ];
  protected $hidden = [
    'password'
  ];

  public function getPayload() {
    return [
      'email' => $this->email,
      'userRole' => $this->user_role,
      'createdAt' => $this->created_at
    ];
  }
}