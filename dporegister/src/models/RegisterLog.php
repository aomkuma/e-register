<?php  

namespace App\Model;
class RegisterLog extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'register_log';
  	protected $primaryKey = 'id';
  	public $timestamps = false;
    
    public function wifi()
    {
  		return $this->hasOne('App\Model\Wifi', 'RegisterLogID');
    }
}