<?php  

namespace App\Model;
class ChoiceIcon extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'choice_icon';
  	protected $primaryKey = 'id';
  	public $timestamps = false;
    protected $fillable = array('id'
    							, 'icon_name'
  							);
    public function choiceIconDetail()
    {
        return $this->hasMany('App\Model\ChoiceIconDetail','icon_id');
    }
}