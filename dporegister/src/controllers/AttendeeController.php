<?php

    namespace App\Controller;
    
    use App\Service\AttendeeService;
    use App\Service\ReportService;
    
    class AttendeeController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }

        public function getAttendeeList($request, $response, $args){
            try{
                $obj = $request->getParsedBody();
                $keyword = $obj['obj']['keyword'];
                $registerType = $obj['obj']['registerType'];
                $years = $obj['obj']['years'];
                $offset = $obj['obj']['offset'];
                $DataList = AttendeeService::getAttendeeList($keyword, $registerType, $years, $offset);
                
                $this->data_result['DATA'] = $DataList;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function updateRewards($request, $response, $args){
            try{
                $obj = $request->getParsedBody();
                $id = $obj['obj']['id'];
                $Rewards = $obj['obj']['Rewards'];
                
                $updateResult = AttendeeService::updateRewards($id, $Rewards);
                
                if(!empty($updateResult)){
                    $this->data_result['DATA'] = $updateResult;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'ไม่สามารถอัพเดทสถานะของผู้ใช้รายนี้ได้';  
                }
                
                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function updateRewardType($request, $response, $args){
            try{

                $obj = $request->getParsedBody();
                $id = $obj['obj']['id'];
                $RewardType = $obj['obj']['RewardType'];
                
                $updateResult = AttendeeService::updateRewardType($id, $RewardType);
                
                if($updateResult){
                    $this->data_result['DATA'] = $updateResult;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'ไม่สามารถอัพเดทสถานะของผู้ใช้รายนี้ได้';  
                }
                
                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function updateEvaluate($request, $response, $args){
            try{
                $obj = $request->getParsedBody();
                $UserID = $obj['obj']['UserID'];
                $Evaluate = $obj['obj']['Evaluate'];
                
                $updateResult = AttendeeService::updateEvaluate($UserID, $Evaluate);
                
                if($updateResult){
                    $this->data_result['DATA'] = $updateResult;
                }else{
                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'ไม่สามารถอัพเดทสถานะของผู้ใช้รายนี้ได้';  
                }
                
                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }
    }

