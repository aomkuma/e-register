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
               	$DataList = EvaluateService::getQuestions();
                $this->data_result['DATA'] = $DataList;

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
                // print_r($obj);exit;
                $UserID = $obj['obj']['UserID'];
                $ResponseType = $obj['obj']['ResponseType'];
                $responseDate = date('Y-m-d H:i:s');
                foreach ($DoQuestions as $key => $value) {
                    $res = EvaluateService::saveEvaluate($UserID, $value, $responseDate, $ResponseType);
                }
                
                // Update Evaluate Status
                EvaluateService::updateAttendeeEvaluateStatus($UserID);
                $attendee = EvaluateService::getAttendee($UserID);
                // print_r($attendee);

                if(!empty(trim($attendee['IDCard']))){
                    // echo $attendee['IDCard'];
                // exit;
                    $Wifi = EvaluateService::getWifi($UserID);
                    
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