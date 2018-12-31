<?php
    
    namespace App\Service;
    
    use App\Model\Attendee;

    use Illuminate\Database\Capsule\Manager as DB;
    
    class AttendeeService {
        
        public static function getAttendeeList($keyword, $registerType, $offset){
            $limit = 20;
            $skip = $offset * $limit;
            $total = Attendee::count();
            
            $DataList = Attendee::where(function($query) use ($keyword, $registerType){
                        if(!empty($keyword)){
                            
                            // Search by id card
                            $query->where('attendee.IDCard', $keyword);

                            // Search by Mobile no.
                            $query->orWhere('attendee.Mobile', $keyword);
                            /*
                            // Search by Firstname/Lastname
                            $UsernameArr = explode(" ", preg_replace('!\s+!', ' ', $keyword));
                            $FirstName = trim($UsernameArr[0]);
                            $LastName = trim($UsernameArr[1]);
                            if(count($UsernameArr) == 1){
                                $query->orWhere('attendee.FirstName', 'LIKE', DB::raw("'%".$FirstName."%'"));
                                $query->orWhere('attendee.LastName', 'LIKE', DB::raw("'%".$FirstName."%'"));
                            }else{
                                $query->orWhere('attendee.FirstName', 'LIKE', DB::raw("'%".$FirstName."%'"));
                                if(!empty($LastName)){
                                    $query->orWhere('attendee.LastName', 'LIKE', DB::raw("'%".$LastName."%'"));
                                }
                            }
                            */
                            // $query->orWhere('attendee.FirstName', 'LIKE', DB::raw("'%".$keyword."%'"));
                            // $query->orWhere('attendee.LastName', 'LIKE', DB::raw("'%".$keyword."%'"));
                            $query->orWhere(DB::raw("CONCAT(FirstName, ' ', LastName)"), 'LIKE', DB::raw("'%".$keyword."%'"));
                        }
                        if(!empty($registerType)){
                            $query->where('attendee.RegisterType', $registerType);
                        }
                        
                        
                    })
                    ->with('wifi')
                    ->skip($skip)
                    ->take($limit)
                    ->orderBy('UserID', 'DESC')
                    ->get();

            $offset += 1;
            $continueLoad = true;
            if(ceil($total / $limit) == $offset){
                $continueLoad = false;
            }

            return [ 'DataList'=>$DataList, 'offset'=>$offset, 'continueLoad'=>$continueLoad, 'totalData'=>$total ];
        }

        public static function updateRewards($UserID, $Rewards){
            $attendee = Attendee::find($UserID);
            $attendee->Rewards = $Rewards;
            if(empty($Rewards)){
                //$attendee->RewardType = '';
                if($attendee->RewardType == ''){
                    $attendee->RewardType = 'NORMAL';    
                }
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

        public static function updateRewardType($UserID, $RewardType){
            $attendee = Attendee::find($UserID);
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