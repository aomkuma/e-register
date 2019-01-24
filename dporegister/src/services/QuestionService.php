<?php
    
    namespace App\Service;
    use App\Model\QuestionYear;
    use App\Model\QuestionSection;
    use App\Model\ChoiceIcon;
    use App\Model\Question;
    use App\Model\Choice;
    use App\Model\ChoiceIconDetail;

    use Illuminate\Database\Capsule\Manager as DB;
    
    class QuestionService {

        
        public static function getChoiceIconList($question_year_id){
            
            return ChoiceIcon::with('choiceIconDetail')->get();
        }

        public static function getQuestionDetailList($question_year_id){
            
            return QuestionSection::with('questions')
                    ->where('question_year_id', $question_year_id)
                    ->get();
        }
        
        public static function getQuestionYearList(){
            
            return QuestionYear::all();
                        
        }

        public static function getQuestionYear($id){
            
            return QuestionYear::find($id);
                        
        }

        public static function getQuestionSection($id){
            
            return QuestionSection::find($id);
                        
        }

        public static function getQuestionDataList($QuestionSection){
            
            return Question::where('QuestionsSection', $QuestionSection)
                    ->with(['choiceList' => function ($query) {
                        $query->where('DisplayType', '<>', 'NON');
                        $query->orderBy('ChoiceOrder', 'ASC');
                     }])
                    ->orderBy('QuestionNo', 'ASC')
                    ->get();
                        
        }

        public static function updateQuestionYear($obj){
            $model = QuestionYear::create($obj);
            return $model->id;
        }

        public static function updateQuestionSection($obj){
            if(empty($obj['id'])){
                $model = QuestionSection::create($obj);
                return $model->id;
            }else{
                $model = QuestionSection::find($obj['id'])->update($obj);
                return $obj['id'];
            }
        }

        public static function updateQuestionData($obj){
            if(empty($obj['QuestionID'])){
                $model = Question::create($obj);
                return $model->QuestionID;
            }else{
                $model = Question::find($obj['QuestionID'])->update($obj);
                return $obj['QuestionID'];
            }
        }

        public static function updateChoiceData($obj){
            if(empty($obj['ChoiceID'])){
                $model = Choice::create($obj);
                return $model->id;
            }else{
                $model = Choice::find($obj['ChoiceID'])->update($obj);
                return $obj['ChoiceID'];
            }
        }

        public static function updateIcon($obj){
            if(empty($obj['id'])){
                $model = ChoiceIcon::create($obj);
                return $model->id;
            }else{
                $model = ChoiceIcon::find($obj['id'])->update($obj);
                return $obj['id'];
            }
        }

        public static function updateIconDetail($obj){
            if(empty($obj['id'])){
                $model = ChoiceIconDetail::create($obj);
                return $model->id;
            }else{
                $model = ChoiceIconDetail::find($obj['id'])->update($obj);
                return $obj['id'];
            }
        }

        public static function removeIconDetail($id){
            return ChoiceIconDetail::find($id)->delete();
        }

        public static function removeQuestionData($QuestionID){
            Choice::where('QuestionID', $QuestionID)->delete();
            return Question::find($QuestionID)->delete();
        }

        
    }

?>