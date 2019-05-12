<?php  

namespace App\Model;
class Suggestion extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'suggestion';
  	protected $primaryKey = 'id';
  	public $timestamps = false;
  	protected $fillable = array('id'
  								, 'user_id'
  								, 'years'
  								, 'suggestion'
  							);
}