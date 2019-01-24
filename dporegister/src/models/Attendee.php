<?php  

namespace App\Model;
class Attendee extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'attendee';
  	protected $primaryKey = 'UserID';
  	public $timestamps = false;
  	
    public function wifi()
    {
  		return $this->hasOne('App\Model\Wifi', 'UserID');
    }
}