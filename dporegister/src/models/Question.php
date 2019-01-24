<?php  

namespace App\Model;
class Question extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'question';
  	protected $primaryKey = 'QuestionID';
  	public $timestamps = false;
  	protected $fillable = array('QuestionID'
  								, 'QuestionYearID'
  								, 'Questions'
  								, 'QuestionsSection'
  								, 'QuestionNo'
  								, 'QuestionType'
  								, 'DisplayType'
  								, 'background_img'
  								, 'actives'
  							);

    public function choiceList()
    {
        return $this->hasMany('App\Model\Choice','QuestionID');
    }
}