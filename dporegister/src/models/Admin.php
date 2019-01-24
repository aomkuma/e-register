<?php  

namespace App\Model;
class Admin extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'admin';
  	protected $primaryKey = 'AdminID';
  	public $timestamps = false;
    
}