<?php
    
    namespace App\Service;
    
    use App\Model\Question;
    use App\Model\QuestionYear;
    use App\Model\QuestionSection;
    use App\Model\Choice;
    use App\Model\QuestionResponse;
    use App\Model\Attendee;
    use App\Model\Wifi;
    use App\Model\RegisterLog;

    use Illuminate\Database\Capsule\Manager as DB;
    
    class EvaluateService {

        public static function getQuestionYear($years){
            return QuestionYear::where('years', $years)
                    ->first();
        }

        public static function getQuestionSection($question_year_id){
            return QuestionSection::where('question_year_id', $question_year_id)
                    ->orderBy('order_no')
                    ->get();
        }
        
        public static function getQuestions($QuestionYearID, $QuestionSection){
            return Question::with(['choiceList' => function ($query) {
                        $query->where('DisplayType', '<>', 'NON');
                        $query->orderBy('ChoiceOrder', 'ASC');
                     }])
                    ->where('QuestionYearID', $QuestionYearID)
                    ->where('QuestionsSection', $QuestionSection)
                    // ->orderBy('QuestionsSection', 'ASC')
                    ->orderBy('QuestionNo', 'ASC')
                    ->get();
        }

        public static function saveEvaluate($UserID, $obj, $ResponseDate, $ResponseType){
            $model =  new QuestionResponse;
            $model->UserID = $UserID;
            $model->QuestionID = $obj['QuestionID'];
            $model->Answers = $obj['answer'];
            $model->ResponseDate = $ResponseDate;
            if(!empty($ResponseType)){
                $model->ResponseType = $ResponseType;
            }
            return $model->save();
        }

        public static function updateAttendeeEvaluateStatus($UserID){
            $years = date('Y');
            $model = RegisterLog::where('user_id', $UserID)->where('years', $years)->first();
            $model->Evaluate = 'Y';
            return $model->save();
        }

        public static function getWifi($UserID){
            $model = Wifi::where('InUse', 'N')->first();
            $model->InUse = 'Y';
            $model->UserID = $UserID;
            $model->save();
            return $model;
        }

        public static function getAttendee($UserID){

            $model = Attendee::find($UserID);
            return $model;
        }

    }