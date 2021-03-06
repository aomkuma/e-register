<?php

	namespace App\Controller;
    
    use App\Service\EvaluateService;
    
    class EvaluateController extends Controller {
        
        protected $logger;
        protected $db;
        protected $sms;
        
        public function __construct($logger, $db, $sms){
            $this->logger = $logger;
            $this->db = $db;
            $this->sms = $sms;
        }

        public function getQuestions($request, $response, $args){
        	//         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');

            try{
                $years = date('Y') + 543;

                $QuestionYear = EvaluateService::getQuestionYear($years);
               	
                // Get section by year
                $QuestionSection = EvaluateService::getQuestionSection($QuestionYear['id']);
                $_List = [];
                foreach ($QuestionSection as $key => $value) {
                    $DataList = EvaluateService::getQuestions($QuestionYear['id'], $value['id']);
                    foreach ($DataList as $_key => $_value) {
                        array_push($_List, $_value);
                    }
                    
                }

                $this->data_result['DATA']['Questions'] = $_List;
                $this->data_result['DATA']['QuestionSection'] = $QuestionSection;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function sendEvaluate($request, $response, $args){
    //                 error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');

            try{
                $obj = $request->getParsedBody();
                $DoQuestions = $obj['obj']['DoQuestions'];
                $Suggestion = trim($obj['obj']['Suggestion']);
                // print_r($obj);exit;
                $UserID = $obj['obj']['UserID'];
                $ResponseType = $obj['obj']['ResponseType'];
                $responseDate = date('Y-m-d H:i:s');
                foreach ($DoQuestions as $key => $value) {
                    $res = EvaluateService::saveEvaluate($UserID, $value, $responseDate, $ResponseType);
                }
                
                // Update Evaluate Status
                $register_log_id = EvaluateService::updateAttendeeEvaluateStatus($UserID);

                if(!empty($Suggestion)){

                    $SuggestionData = [];
                    $SuggestionData['user_id'] = $UserID;                
                    $SuggestionData['years'] = (date('Y') + 543);                
                    $SuggestionData['suggestion'] = $Suggestion;                
                    EvaluateService::addSuggestion($SuggestionData);

                }

                $attendee = EvaluateService::getAttendee($UserID);
                // print_r($attendee);

                if(!empty(trim($attendee['IDCard']))){
                    // echo $attendee['IDCard'];
                // exit;
                    $Wifi = EvaluateService::getWifi($UserID, $register_log_id);
                    
                    if($res){
                        // send wifi
                       // print_r($attendee);
                        $smsContent = 'รหัสเข้าใช้งาน Wifi ของหมายเลขบัตรประชาชน ' . $attendee['IDCard'] . ' คือ username : ' . $Wifi['WifiUser'] .' password : ' . $Wifi['WifiPassword'];
                        $smsResult = $this->sendSMS($attendee['Mobile'], $smsContent);
                        // exit;
                        // $smsResult = true;
                        $this->logger->info('SMS Result : ' . $smsResult); 
                        if($smsResult !== false){
                            $this->logger->info('SMS Result Success '); 
                            $this->data_result['DATA'] = 'ขอบคุณที่ร่วมทำแบบสอบถาม กรุณาตรวจสอบรหัสเข้าใช้งาน Wifi ที่ส่งไปยังเบอร์โทรศัพท์ที่ท่านได้ลงทะเบียนไว้';
                        }
                    }
                }else{
                    $this->data_result['DATA'] = 'ขอบคุณที่ร่วมทำแบบสอบถาม';
                }
                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        private function sendSMS($receiver, $content){
            $sms = new SMSController($this->logger, $this->sms);
            $sms->setSmsReceiver($receiver);
            $sms->setSmsDesc($content);
            $sms->sendSMS();
        }
    }