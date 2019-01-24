<?php

	namespace App\Model;
	class Amphur extends \Illuminate\Database\Eloquent\Model {  
	  	protected $table = 'amphur';
	  	protected $primaryKey = 'AMPHUR_ID';
	  	public $timestamps = false;
	  	public function districtList()
	    {
	        return $this->hasMany('App\Model\District','AMPHUR_ID');
	    }
	    
}