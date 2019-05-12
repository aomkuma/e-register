<?php
    
    namespace App\Service;
    
    use App\Model\Attendee;
    use App\Model\RegisterLog;

    use Illuminate\Database\Capsule\Manager as DB;
    
    class AttendeeService {
        
        public static function getAttendeeList($keyword, $registerType, $years, $offset){
            $limit = 20;
            $skip = $offset * $limit;
            $total = Attendee::where(function($query) use ($keyword){
                            if(!empty($keyword)){
                                // Search by id card
                                $query->where('attendee.IDCard', $keyword);
                                // Search by Mobile no.
                                $query->orWhere('attendee.Mobile', $keyword);
                                $query->orWhere(DB::raw("CONCAT(FirstName, ' ', LastName)"), 'LIKE', DB::raw("'%".$keyword."%'"));
                            }  
                        })
                        ->where(function($query) use ($registerType, $years){
                            if(!empty($registerType)){
                                $query->where('attendee.RegisterType', $registerType);
                            }  

                            if(!empty($years)){
                                $query->where('register_log.years', $years);
                            }  
                        })
                        ->join("register_log", 'register_log.user_id', '=', 'attendee.UserID')
                        ->count();
            
            $DataList = RegisterLog::select("attendee.*"
                            ,"register_log.id"
                            ,"register_log.years"
                            ,"register_log.register_date"
                            ,"register_log.EvaluateType"
                            ,"register_log.Evaluate"
                            ,"register_log.Rewards"
                            ,"register_log.RewardType"
                            ,"register_log.RewardDate"
                            ,"register_log.RegisterType"
                        )
                        ->where(function($query) use ($keyword){
                            if(!empty($keyword)){
                                // Search by id card
                                $query->where('attendee.IDCard', $keyword);
                                // Search by Mobile no.
                                $query->orWhere('attendee.Mobile', $keyword);
                                $query->orWhere(DB::raw("CONCAT(FirstName, ' ', LastName)"), 'LIKE', DB::raw("'%".$keyword."%'"));
                            }  
                        })
                        ->where(function($query) use ($registerType, $years){
                            if(!empty($registerType)){
                                $query->where('attendee.RegisterType', $registerType);
                            }  

                            if(!empty($years)){
                                $query->where('register_log.years', $years);
                            }  
                        })
                        ->join("attendee", 'register_log.user_id', '=', 'attendee.UserID')
                        ->with('wifi')
                        ->skip($skip)
                        ->take($limit)
                        ->orderBy('register_log', 'DESC')
                        ->orderBy('UserID', 'DESC')
                        ->get();

            $offset += 1;
            $continueLoad = true;
            if(ceil($total / $limit) == $offset){
                $continueLoad = false;
            }

            return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad, 'totalData'=>$total ];
        }

        public static function updateRewards($id, $Rewards){
            $attendee = RegisterLog::find($id);
            $attendee->Rewards = $Rewards;
            if(empty($Rewards)){
                //$attendee->RewardType = '';
                // if($attendee->RewardType == ''){
                //     $attendee->RewardType =  'NORMAL';    
                // }
                $attendee->RewardType =  '';    
                $attendee->RewardDate = NULL;
            }else{
                if($attendee->RewardType == ''){
                    $attendee->RewardType = 'NORMAL';    
                }
                $attendee->RewardDate = date('Y-m-d H:i:s');    
            }
            
            $attendee->save();
            return $attendee;
        }

        public static function updateRewardType($id, $RewardType){
            $attendee = RegisterLog::find($id);
            $attendee->RewardType = $RewardType;
            return $attendee->save();
        }

        public static function updateEvaluate($UserID, $Evaluate){
            $attendee = Attendee::find($UserID);
            $attendee->Evaluate = $Evaluate;
            return $attendee->save();
        }

    }

?>