<?php  

namespace App\Model;
class QuestionSection extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'question_section';
  	protected $primaryKey = 'id';
  	public $timestamps = false;
    protected $fillable = array('id'
  								, 'question_year_id'
  								, 'section'
  								, 'order_no'
  							);

    public function questions()
    {
        return $this->hasMany('App\Model\Question', 'QuestionsSection');
    }

}