<?php  

namespace App\Model;
class Province extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'province';
  	protected $primaryKey = 'PROVINCE_ID';
  	public $timestamps = false;
  	public function amphurList()
    {
        return $this->hasMany('App\Model\Amphur','PROVINCE_ID');
    }
    
}