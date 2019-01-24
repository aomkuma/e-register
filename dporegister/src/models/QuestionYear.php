<?php  

namespace App\Model;
class QuestionYear extends \Illuminate\Database\Eloquent\Model {  
  	protected $table = 'question_year';
  	protected $primaryKey = 'id';
  	public $timestamps = false;
    protected $fillable = array('id'
  								, 'years'
  								, 'actives'
  							);

}