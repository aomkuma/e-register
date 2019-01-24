<?php  

namespace App\Model;
class ChoiceIconDetail extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'choice_icon_detail';
  	protected $primaryKey = 'id';
  	public $timestamps = false;
    protected $fillable = array('id'
    							, 'icon_id'
  								, 'img_path'
  								, 'points'
                  , 'icon_description'
  								, 'order_no'
  							);

}