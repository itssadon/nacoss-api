<?php
namespace NACOSS\Models;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
  /**
   * The table associated with the model.
   *
   * @var string
   */
  protected $table = 'message_template';

  /**
   * Turn off the created_at & updated_at columns
   * @var boolean
   */
  public $timestamps = false;

  /**
   * Fields that are mass assignable
   * @var array
   */
  protected $fillable = [
    'id', 'description', 'subject', 'body'
  ];

}
