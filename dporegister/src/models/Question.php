<?php  

namespace App\Model;
class Question extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'question';
  	protected $primaryKey = 'QuestionID';
  	public $timestamps = false;
    public function choiceList()
    {
        return $this->hasMany('App\Model\Choice','QuestionID');
    }
}