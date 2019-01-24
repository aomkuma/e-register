<?php  

namespace App\Model;
class Choice extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'choice';
  	protected $primaryKey = 'ChoiceID';
  	public $timestamps = false;

  	protected $fillable = array('ChoiceID'
  								, 'choice_icon_id'
  								, 'ChoiceDesc'
  								, 'ChoicePoint'
  								, 'ImgPath'
  								, 'ChoiceOrder'
  								, 'DisplayType'
  								, 'QuestionID'
  							);
    
}