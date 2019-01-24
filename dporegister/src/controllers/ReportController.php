<?php

    namespace App\Controller;
    
    use App\Service\ReportService;
    use PHPExcel;

    class ReportController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }

        public function loadSummary($request, $response, $args){
            try{
                $summaryRegister = ReportService::CountRegister();
                $summaryEvaluatebySystem =  count(ReportService::CountEvaluateBySystem());
                $summaryEvaluatebyManual =  count(ReportService::CountEvaluateByManual());
                $summaryEvaluatebyAdmin =  count(ReportService::CountEvaluateByAdmin());
                $this->data_result['DATA']['CountRegister'] = $summaryRegister;
                $this->data_result['DATA']['CountEvaluate'] = ($summaryEvaluatebySystem + $summaryEvaluatebyManual + $summaryEvaluatebyAdmin);

                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function exportExcel($request, $response, $args){
            // error_reporting(E_ERROR);
            // error_reporting(E_ALL);
            // ini_set('display_errors','On');           
            try{
                
                $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
                $catch_result = \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

                $objPHPExcel = new PHPExcel();
                
                // Get Data Total Register 
                $resultS1 = $this->QuerySheet1($objPHPExcel);
                $objPHPExcel = $resultS1[0];
                $totalEvaluateSystem = $resultS1[1];
                $totalEvaluateManual = $resultS1[2];

                // Get Data Question section 1  (SYSTEM)
                $sheetIndex = 1;
                $sheetName = "รายงานสรุปส่วนที่ 1 ผ่านระบบ";
                $registerType = 'SYSTEM';
                $objPHPExcel = $this->QuerySheet2($totalEvaluateSystem, $registerType, $objPHPExcel, $sheetIndex, $sheetName);

                // // Get Data Question section 2  (SYSTEM)
                $sheetIndex = 2;
                $sheetName = "รายงานสรุปส่วนที่ 2 ผ่านระบบ";
                $registerType = 'SYSTEM';
                $objPHPExcel = $this->QuerySheet3($totalEvaluateSystem, $registerType, $objPHPExcel, $sheetIndex, $sheetName);

                // // Get Data Question section 1  (MANUAL)
                $sheetIndex = 3;
                $sheetName = "รายงานสรุปส่วนที่ 1 Manual";
                $registerType = 'MANUAL';
                $objPHPExcel = $this->QuerySheet4($totalEvaluateManual, $registerType, $objPHPExcel, $sheetIndex, $sheetName);

                // // Get Data Question section 2  (MANUAL)                
                $sheetIndex = 4;
                $sheetName = "รายงานสรุปส่วนที่ 2 Manual";
                $registerType = 'MANUAL';
                $objPHPExcel = $this->QuerySheet3($totalEvaluateManual, $registerType, $objPHPExcel, $sheetIndex, $sheetName);

                // // Get Data Question section 1  (ALL)
                $sheetIndex = 5;
                $sheetName = "รายงานสรุปส่วนที่ 1 รวม";
                $registerType = '';
                $objPHPExcel = $this->QuerySheet5($totalEvaluateSystem, $totalEvaluateManual, $objPHPExcel, $sheetIndex, $sheetName);

                // // Get Data Question section 2  (ALL)
                $sheetIndex = 6;
                $sheetName = "รายงานสรุปส่วนที่ 2 รวม";
                $registerType = '';
                $totalEvaluate = $totalEvaluateManual + $totalEvaluateSystem;
                $objPHPExcel = $this->QuerySheet3($totalEvaluate, $registerType, $objPHPExcel, $sheetIndex, $sheetName);

                // // Get Data Attendee Details
                $sheetIndex = 7;
                $sheetName = "สรุปรายงานข้อมูลที่เก็บ";
                $objPHPExcel = $this->QuerySheetSummary($objPHPExcel, $sheetIndex, $sheetName);                

                $filename =  'report_evaluation_' . date('YmdHis') . '.xlsx';
                $filepath = '../../../e-register/downloads/' . $filename;
                
                $objWriter = \PHPExcel_IOFactory::createWriter ( $objPHPExcel, 'Excel2007' );
                $objWriter->setPreCalculateFormulas(); 
                
                $objWriter->save ( $filepath );
                
                $this->data_result['DATA'] = $filename;
                
                return $this->returnResponse(200, $this->data_result, $response);

            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }


        private function QuerySheetSummary($objPHPExcel, $sheetIndex, $sheetName){
            
            $attendeeList = ReportService::getAttendeeDetail();
            
            // print_r($attendeeList);
            // exit();
            $index = 1;
            $row_index = 1;
            $objPHPExcel->createSheet($sheetIndex);
            $objPHPExcel->setActiveSheetIndex($sheetIndex);
            $objPHPExcel->getActiveSheet()->setTitle($sheetName);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, "ลำดับที่สมัคร"); 
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "วันที่สมัคร");
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "เวลาที่สมัคร"); 
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_index, "ชื่อ"); 
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_index, "สกุล"); 
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_index, "วันเดือนปีเกิด"); 
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_index, "อายุ"); 
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $row_index, "เพศ"); 
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $row_index, "บ้านเลขที่");
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $row_index, "หมู่ที่");
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $row_index, "ถนน");
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $row_index, "ซอย");
            $objPHPExcel->getActiveSheet()->setCellValue('M' . $row_index, "ตำบล");
            $objPHPExcel->getActiveSheet()->setCellValue('N' . $row_index, "อำเภอ");
            $objPHPExcel->getActiveSheet()->setCellValue('O' . $row_index, "จังหวัด"); 
            $objPHPExcel->getActiveSheet()->setCellValue('P' . $row_index, "รหัสไปรษณีย์"); 
            $objPHPExcel->getActiveSheet()->setCellValue('Q' . $row_index, "เบอร์โทร"); 
            $objPHPExcel->getActiveSheet()->setCellValue('R' . $row_index, "email"); 
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $row_index, "เลขที่บัตร ปปช."); 
            $objPHPExcel->getActiveSheet()->setCellValue('T' . $row_index, "สถานะการลงทะเบียน"); 
            $objPHPExcel->getActiveSheet()->setCellValue('U' . $row_index, "สถานะการทำแบบประเมิน"); 
            $objPHPExcel->getActiveSheet()->setCellValue('V' . $row_index, "สถานะการรับของรางวัล"); 
            $objPHPExcel->getActiveSheet()->setCellValue('W' . $row_index, "วันที่รับของ");
            $objPHPExcel->getActiveSheet()->setCellValue('X' . $row_index, "เวลาที่รับของ"); 
            $objPHPExcel->getActiveSheet()->setCellValue('Y' . $row_index, "ประเภทของรางวัล"); 
            $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row_index, "Username Wifi"); 
            $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row_index, "Password Wifi"); 
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);

            $objPHPExcel->getActiveSheet()
                ->getStyle("A".$row_index.":AA".$row_index)
                ->applyFromArray(array(                  
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                         ),'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'D9DDDE')
                        )
                    )
                );
            $col_arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA'];
            $row_index++;
            foreach($attendeeList as $k => $v){
                // print_r($v);
                $age = '';
                if(!empty($v['Birthdate']) && $v['Birthdate'] != '0000-00-00'){
                    $d1 = new \DateTime(date('Y-m-d'));
                    $d2 = new \DateTime($v['Birthdate']);
                    $diff = $d2->diff($d1);
                    $age = $diff->y;
                }

                $gender = '';
                if($v['Gender'] == 'M'){
                    $gender = 'ชาย';
                }else if($v['Gender'] == 'F'){
                    $gender = 'หญิง';
                }

                $registerDate = $this->getReportDate($v['CreateDate']);
                $registerTime = explode(' ', $v['CreateDate'])[1];

                $rewardDate = (!empty($v['RewardDate'])?$this->getReportDate($v['RewardDate']):'');
                $rewardTime = explode(' ', $v['RewardDate'])[1];                
                // $addresses = $v['AddressNo'] . ' หมู่ ' . $v['Moo']. ' ถนน '. $v['Street'] . ' ซอย '.
                //             $v['Soi'] . ' แขวง/ตำบล' . $v['District'] . ' เขต/อำเภอ ' . $v['SubProvince'] . ' จังหวัด ' . $v['Province'] . $v['Postcode'];
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, $index); 
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $registerDate); 
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $registerTime); 
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $row_index, $v['Firstname']); 
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $row_index, $v['Lastname']); 
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $row_index, (!empty($v['Birthdate']) && $v['Birthdate'] != '0000-00-00'?$this->getReportDate($v['Birthdate']):'')); 
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_index, $age); 
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $row_index, $gender); 
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $row_index, $v['AddressNo'],\PHPExcel_Cell_DataType::TYPE_STRING); 
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $row_index, $v['Moo'],\PHPExcel_Cell_DataType::TYPE_STRING); 
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $row_index, $v['Street']); 
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $row_index, $v['Soi'],\PHPExcel_Cell_DataType::TYPE_STRING); 
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $row_index, $v['District']); 
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $row_index, $v['SubProvince']); 
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $row_index, $v['Province']); 
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $row_index, $v['Postcode']); 
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('Q' . $row_index, $v['Mobile'],\PHPExcel_Cell_DataType::TYPE_STRING); 
                $objPHPExcel->getActiveSheet()->setCellValue('R' . $row_index, $v['Email']); 
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('S' . $row_index, $v['IDCard'],\PHPExcel_Cell_DataType::TYPE_STRING); 
                $objPHPExcel->getActiveSheet()->setCellValue('T' . $row_index, ($v['RegisterType'] == 'SYSTEM'?'ลงทะเบียนล่วงหน้า':'ลงทะเบียนหน้างาน')); 
                $objPHPExcel->getActiveSheet()->setCellValue('U' . $row_index, ($v['Evaluate'] == 'Y'?'ทำแบบประเมินแล้ว':'ยังไม่ได้ทำแบบประเมิน')); 
                $objPHPExcel->getActiveSheet()->setCellValue('V' . $row_index, ($v['Rewards']=='Y'?'รับของแล้ว':'ยังไม่ได้รับของ')); 
                $objPHPExcel->getActiveSheet()->setCellValue('W' . $row_index, $rewardDate); 
                $objPHPExcel->getActiveSheet()->setCellValue('X' . $row_index, $rewardTime); 
                $objPHPExcel->getActiveSheet()->setCellValue('Y' . $row_index, $v['RewardType']); 
                $objPHPExcel->getActiveSheet()->setCellValue('Z' . $row_index, $v['wifi']['WifiUser']); 
                $objPHPExcel->getActiveSheet()->setCellValue('AA' . $row_index, $v['wifi']['WifiPassword']); 
                $index++;
                $row_index++;
            }

            $objPHPExcel->getActiveSheet()->getStyle("A1:AA" . $objPHPExcel->getActiveSheet()->getHighestRow())
                ->applyFromArray($this->getDefaultStyle());


            return $objPHPExcel;
        }

        private function QuerySheet5($totalEvaluateSystem, $totalEvaluateManual, $objPHPExcel, $sheetIndex, $sheetName){
            $totalEvaluate = $totalEvaluateSystem + $totalEvaluateManual;
            $questionList = ReportService::GetQuestion(1);
            $EvaluateList = [];
            foreach($questionList as $k => $v){
                // get question response by question id
                $ChoiceResponse = [];
                $age1 = 0;
                $age2 = 0;
                $age3 = 0;
                $age4 = 0;
                foreach($v['choiceList'] as $ck => $cv){

                    if($v['QuestionID'] == '3'){
                        $responseSys = count(ReportService::GetQuestionResponseMultiChoice($v['QuestionID'], $cv['ChoiceDesc'], 'SYSTEM'));
                        $responseMan = count(ReportService::GetQuestionResponseMultiChoice($v['QuestionID'], $cv['ChoiceDesc'], 'MANUAL'));
                        $response = $responseMan + $responseSys;  

                    }else if($v['QuestionID'] == '32'){

                        $responseSys = count(ReportService::GetQuestionResponseByGender($cv['ChoiceDesc'])); 
                        $responseMan = count(ReportService::GetQuestionResponse($v['QuestionID'], $cv['ChoiceDesc'], 'MANUAL'));       
                        $response = $responseMan + $responseSys;  
                    }else if($v['QuestionID'] == '33'){
                        $responseSys = count(ReportService::GetQuestionResponseByAge($cv['ChoiceDesc']));
                        $responseMan = count(ReportService::GetQuestionResponse($v['QuestionID'], $cv['ChoiceDesc'], 'MANUAL'));       
                        $response = $responseMan + $responseSys;  
                    }else{
                        $responseSys = count(ReportService::GetQuestionResponse($v['QuestionID'], $cv['ChoiceDesc'], 'SYSTEM'));    
                        $responseMan = count(ReportService::GetQuestionResponse($v['QuestionID'], $cv['ChoiceDesc'], 'MANUAL'));    
                        $response = $responseMan + $responseSys;  
                    }
                    
                    $cv['response'] = $response;
                    $ChoiceResponse[] = $cv;
                }

                $v['ChoiceResponse'] = $ChoiceResponse;
                $EvaluateList[] = $v;
            }

            // Gen excel
            $row_index = 2;
            $objPHPExcel->createSheet($sheetIndex);
            $objPHPExcel->setActiveSheetIndex($sheetIndex);
            $objPHPExcel->getActiveSheet()->setTitle($sheetName);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, "ส่วนที่ 1"); 
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(65);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $row_index++;

            foreach($EvaluateList as $k => $v){
                if($v['QuestionsSection'] == '1'){

                    if($v['QuestionID'] == '3'){

                        foreach ($v['ChoiceResponse'] as $ck => $cv) {
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'วัตถุประสงที่มา (' . $cv['ChoiceDesc'] . ')' ); 
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "จำนวน"); 
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $row_index)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "คิดเป็นร้อยละ"); 
                            $objPHPExcel->getActiveSheet()->getStyle('C' . $row_index)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()
                                ->getStyle("A".$row_index.":C".$row_index)
                                ->applyFromArray(array(                  
                                        'alignment' => array(
                                            //'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                                         ),'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'D9DDDE')
                                        )
                                    )
                                );
                            $row_index++;

                            $totalResponse = $cv['response'];//[0]->count_row;
                            $totalResponsePercent = $this->number_format_round($totalResponse * 100 / $totalEvaluate, 2, '.','');
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'เลือก'); 
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $totalResponse); 
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $totalResponsePercent);
                            $row_index++;

                            $totalNonResponse = $totalEvaluate - $totalResponse;//[0]->count_row;
                            $totalNonResponsePercent = $this->number_format_round($totalNonResponse * 100 / $totalEvaluate, 2, '.','');
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'ไม่เลือก'); 
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $totalNonResponse); 
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $totalNonResponsePercent);
                            $row_index++;

                            // Summary
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'รวม');
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, ($totalNonResponse + $totalResponse));
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, ($totalResponsePercent + $totalNonResponsePercent));
                            $row_index++; 
                        }

                    }else{
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, $v['Questions']); 
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "จำนวน"); 
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $row_index)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "คิดเป็นร้อยละ"); 
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $row_index)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()
                            ->getStyle("A".$row_index.":C".$row_index)
                            ->applyFromArray(array(                  
                                    'alignment' => array(
                                        //'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                                     ),'fill' => array(
                                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'D9DDDE')
                                    )
                                )
                            );
                        $row_index++;

                        // Set details
                        $summary = 0;
                        $summaryPercent = 100;  
                        foreach ($v['ChoiceResponse'] as $ck => $cv) {
                            $summary += floatval($cv['response']);
                        }

                        foreach ($v['ChoiceResponse'] as $ck => $cv) {
                            // print_r($cv['response']);
                            $totalResponse = $cv['response'];//[0]->count_row;
                            $totalResponsePercent = ($this->number_format_round(($totalResponse / $summary)  * 100, 2, '.',''));
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, $cv['ChoiceDesc']); 
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $totalResponse); 
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $totalResponsePercent);
                            $row_index++; 
                            // $summary += $totalResponse;
                            // $summaryPercent += $totalResponsePercent;
                        }

                        // Summary
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'รวม');
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $summary);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $summaryPercent);
                        $row_index++; 
                    }
                }
            }

            $objPHPExcel->getActiveSheet()->getStyle("A3:C" . $objPHPExcel->getActiveSheet()->getHighestRow())
                ->applyFromArray($this->getDefaultStyle());

            return $objPHPExcel;
        }

        private function QuerySheet4($totalEvaluate, $registerType, $objPHPExcel, $sheetIndex, $sheetName){
            $questionList = ReportService::GetQuestion(1);
            $EvaluateList = [];
            foreach($questionList as $k => $v){
                // get question response by question id
                $ChoiceResponse = [];
                $age1 = 0;
                $age2 = 0;
                $age3 = 0;
                $age4 = 0;
                foreach($v['choiceList'] as $ck => $cv){
                    if($v['QuestionID'] == '3'){
                        $response = count(ReportService::GetQuestionResponseMultiChoice($v['QuestionID'], $cv['ChoiceDesc'], $registerType));
                    }else{
                        $response = count(ReportService::GetQuestionResponse($v['QuestionID'], $cv['ChoiceDesc'], $registerType));    
                    }
                    
                    $cv['response'] = $response;
                    $ChoiceResponse[] = $cv;
                }

                $v['ChoiceResponse'] = $ChoiceResponse;
                $EvaluateList[] = $v;
            }

            // Gen excel
            $row_index = 2;
            $objPHPExcel->createSheet($sheetIndex);
            $objPHPExcel->setActiveSheetIndex($sheetIndex);
            $objPHPExcel->getActiveSheet()->setTitle($sheetName);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, "ส่วนที่ 1"); 
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(65);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $row_index++;

            foreach($EvaluateList as $k => $v){
                if($v['QuestionsSection'] == '1'){

                    if($v['QuestionID'] == '3'){

                        foreach ($v['ChoiceResponse'] as $ck => $cv) {
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'วัตถุประสงที่มา (' . $cv['ChoiceDesc'] . ')' ); 
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "จำนวน"); 
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $row_index)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "คิดเป็นร้อยละ"); 
                            $objPHPExcel->getActiveSheet()->getStyle('C' . $row_index)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()
                                ->getStyle("A".$row_index.":C".$row_index)
                                ->applyFromArray(array(                  
                                        'alignment' => array(
                                            //'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                                         ),'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'D9DDDE')
                                        )
                                    )
                                );
                            $row_index++;

                            $totalResponse = $cv['response'];//[0]->count_row;
                            $totalResponsePercent = $this->number_format_round($totalResponse * 100 / $totalEvaluate, 2, '.','');
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'เลือก'); 
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $totalResponse); 
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $totalResponsePercent);
                            $row_index++;

                            $totalNonResponse = $totalEvaluate - $totalResponse;//[0]->count_row;
                            $totalNonResponsePercent = $this->number_format_round($totalNonResponse * 100 / $totalEvaluate, 2, '.','');
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'ไม่เลือก'); 
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $totalNonResponse); 
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $totalNonResponsePercent);
                            $row_index++;

                            // Summary
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'รวม');
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, ($totalNonResponse + $totalResponse));
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, ($totalResponsePercent + $totalNonResponsePercent));
                            $row_index++; 
                        }

                    }else{
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, $v['Questions']); 
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "จำนวน"); 
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $row_index)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "คิดเป็นร้อยละ"); 
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $row_index)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()
                            ->getStyle("A".$row_index.":C".$row_index)
                            ->applyFromArray(array(                  
                                    'alignment' => array(
                                        //'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                                     ),'fill' => array(
                                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'D9DDDE')
                                    )
                                )
                            );
                        $row_index++;

                        // Set details
                        $summary = 0;
                        $summaryPercent = 100;  
                        foreach ($v['ChoiceResponse'] as $ck => $cv) {
                            $summary += floatval($cv['response']);
                        }

                        foreach ($v['ChoiceResponse'] as $ck => $cv) {
                            // print_r($cv['response']);
                            $totalResponse = $cv['response'];//[0]->count_row;
                            $totalResponsePercent = ($this->number_format_round(($totalResponse / $summary)  * 100, 2, '.',''));
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, $cv['ChoiceDesc']); 
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $totalResponse); 
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $totalResponsePercent);
                            $row_index++; 
                            // $summary += $totalResponse;
                            // $summaryPercent += $totalResponsePercent;
                        }

                        // Summary
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'รวม');
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $summary);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $summaryPercent);
                        $row_index++; 
                    }
                }
            }

            $objPHPExcel->getActiveSheet()->getStyle("A3:C" . $objPHPExcel->getActiveSheet()->getHighestRow())
                ->applyFromArray($this->getDefaultStyle());

            return $objPHPExcel;
        }

        private function QuerySheet3($totalEvaluate, $registerType, $objPHPExcel, $sheetIndex, $sheetName){
            
            $row_index = 3;
            $objPHPExcel->createSheet($sheetIndex);
            $objPHPExcel->setActiveSheetIndex($sheetIndex);
            $objPHPExcel->getActiveSheet()->setTitle($sheetName);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(36);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);

            $objPHPExcel->getActiveSheet()
                                ->getStyle("A1:L2")
                                ->applyFromArray(array(                  
                                        'alignment' => array(
                                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER
                                         ),'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'D9DDDE')
                                        )
                                    )
                                );
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "รายการประเมิน"); 
            $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "จำนวนคนที่ประเมิน"); 
            $objPHPExcel->getActiveSheet()->mergeCells('B1:G1');
            $objPHPExcel->getActiveSheet()->setCellValue('H1', "คิดเป็นร้อยละ"); 
            $objPHPExcel->getActiveSheet()->mergeCells('H1:L1');
            $objPHPExcel->getActiveSheet()->setCellValue('B2', "มาก"); 
            $objPHPExcel->getActiveSheet()->setCellValue('C2', "ปานกลาง"); 
            $objPHPExcel->getActiveSheet()->setCellValue('D2', "น้อย"); 
            $objPHPExcel->getActiveSheet()->setCellValue('E2', "ควรปรับปรุง"); 
            $objPHPExcel->getActiveSheet()->setCellValue('F2', "ไม่ได้เข้าร่วมกิจกรรม");
            $objPHPExcel->getActiveSheet()->setCellValue('G2', "จำนวนรวม");
            $objPHPExcel->getActiveSheet()->setCellValue('H2', "มาก"); 
            $objPHPExcel->getActiveSheet()->setCellValue('I2', "ปานกลาง"); 
            $objPHPExcel->getActiveSheet()->setCellValue('J2', "น้อย"); 
            $objPHPExcel->getActiveSheet()->setCellValue('K2', "ควรปรับปรุง"); 
            $objPHPExcel->getActiveSheet()->setCellValue('L2', "ไม่ได้เข้าร่วมกิจกรรม");
            
            $objPHPExcel->getActiveSheet()
                                ->getStyle("H1:L2")
                                ->applyFromArray(array(                  
                                        'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'FFE29C')
                                        )
                                    )
                                );   

            $topic = ["ความพึงพอใจด้านกิจกรรมต่างๆภายในงาน", "ความพึงพอใจด้านสถานที่","ความพึงพอใจด้านการบริการและอื่นๆ","ภาพรวมของการจัดงาน"];
            for($i = 0; $i < 4; $i++){
                $questionList = ReportService::GetQuestion(($i + 2));
                $EvaluateList = [];
                foreach($questionList as $k => $v){
                    $ChoiceResponse = [];
                    foreach($v['choiceList'] as $ck => $cv){
                        $response = count(ReportService::GetQuestionResponse($v['QuestionID'], $cv['ChoiceDesc'], $registerType)); 
                        $cv['response'] = $response;
                        $ChoiceResponse[] = $cv;   
                    }
                    $v['ChoiceResponse'] = $ChoiceResponse;
                    $EvaluateList[] = $v;
                }

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, $topic[$i]);        
                $objPHPExcel->getActiveSheet()
                                ->getStyle("A" . $row_index.":A" . $row_index)
                                ->applyFromArray(array(                  
                                        'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => '81E3F2')
                                        )
                                    )
                                );            
                $row_index++;
                $col_arr = ['B','C','D','E','F','G']; 
                $col_percent_arr = ['H','I','J','K','L'];
                $col_summary_arr = [0,0,0,0,0,0]; 
                $col_summarypercent_arr = [0,0,0,0,0]; 
                $question_no = 1;
                foreach($EvaluateList as $k => $v){
                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, $question_no . '. ' . $v['Questions']);
                    $col_index = 0;
                    $totalResponse = 0;
                    foreach ($v['ChoiceResponse'] as $ck => $cv) {
                        // print_r( $cv['ChoiceID']. $cv['ChoiceDesc'].' : ' .$cv['response']);
                        // echo $col_arr[$col_index] . $row_index;
                        // echo "\n\r";
                        $response = $cv['response'];
                        $responsePercent = $this->number_format_round(($response *100) / $totalEvaluate, 2, '.', '');
                        $totalResponse += $response;
                        $objPHPExcel->getActiveSheet()->setCellValue($col_arr[$col_index] . $row_index, $response);
                        $objPHPExcel->getActiveSheet()->setCellValue($col_percent_arr[$col_index] . $row_index, $responsePercent);
                        $col_summary_arr[$col_index] += $response;
                        $col_summarypercent_arr[$col_index] += $responsePercent;
                        $col_index++;
                    }
                    // Set total in cell 'G'
                    $objPHPExcel->getActiveSheet()->setCellValue('G' . $row_index, $totalResponse);
                    $col_summary_arr[$col_index] += $totalResponse;
                    $row_index++;
                    $question_no++;
                }
                // Vertical Summary
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'รวม');
                for($k = 0; $k < count($col_summary_arr); $k++){
                    $objPHPExcel->getActiveSheet()->setCellValue($col_arr[$k] . $row_index, $col_summary_arr[$k]);
                }

                for($k = 0; $k < count($col_summarypercent_arr); $k++){
                    $objPHPExcel->getActiveSheet()->setCellValue($col_percent_arr[$k] . $row_index, $col_summarypercent_arr[$k]);
                }                

                $objPHPExcel->getActiveSheet()
                                ->getStyle("A".$row_index.":L".$row_index)
                                ->applyFromArray(array(                  
                                        'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'FFE29C')
                                        )
                                    )
                                );   
                $row_index++;

            }
            
            // exit;
            $objPHPExcel->getActiveSheet()->getStyle("A1:L" . $objPHPExcel->getActiveSheet()->getHighestRow())
                ->applyFromArray($this->getDefaultStyle());

            return $objPHPExcel;            

        }

        private function QuerySheet2($totalEvaluate, $registerType, $objPHPExcel, $sheetIndex, $sheetName){
            $questionList = ReportService::GetQuestion(1);
            $EvaluateList = [];
            foreach($questionList as $k => $v){
                // get question response by question id
                $ChoiceResponse = [];
                $age1 = 0;
                $age2 = 0;
                $age3 = 0;
                $age4 = 0;
                foreach($v['choiceList'] as $ck => $cv){
                    if($v['QuestionID'] == '3'){
                        $response = count(ReportService::GetQuestionResponseMultiChoice($v['QuestionID'], $cv['ChoiceDesc'], $registerType));
                    }else if($v['QuestionID'] == '32'){
                        $response = count(ReportService::GetQuestionResponseByGender($cv['ChoiceDesc']));    
                    }else if($v['QuestionID'] == '33'){
                        $response = count(ReportService::GetQuestionResponseByAge($cv['ChoiceDesc']));    
                    }else{
                        $response = count(ReportService::GetQuestionResponse($v['QuestionID'], $cv['ChoiceDesc'], $registerType));    
                    }
                    
                    $cv['response'] = $response;
                    $ChoiceResponse[] = $cv;
                }

                $v['ChoiceResponse'] = $ChoiceResponse;
                $EvaluateList[] = $v;
            }

            // Gen excel
            $row_index = 2;
            $objPHPExcel->createSheet($sheetIndex);
            $objPHPExcel->setActiveSheetIndex($sheetIndex);
            $objPHPExcel->getActiveSheet()->setTitle($sheetName);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, "ส่วนที่ 1"); 
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(65);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14);
            $row_index++;

            foreach($EvaluateList as $k => $v){
                if($v['QuestionsSection'] == '1'){

                    if($v['QuestionID'] == '3'){

                        foreach ($v['ChoiceResponse'] as $ck => $cv) {
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'วัตถุประสงที่มา (' . $cv['ChoiceDesc'] . ')' ); 
                            $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "จำนวน"); 
                            $objPHPExcel->getActiveSheet()->getStyle('B' . $row_index)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "คิดเป็นร้อยละ"); 
                            $objPHPExcel->getActiveSheet()->getStyle('C' . $row_index)->getFont()->setBold(true);
                            $objPHPExcel->getActiveSheet()
                                ->getStyle("A".$row_index.":C".$row_index)
                                ->applyFromArray(array(                  
                                        'alignment' => array(
                                            //'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                                         ),'fill' => array(
                                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                            'color' => array('rgb' => 'D9DDDE')
                                        )
                                    )
                                );
                            $row_index++;

                            $totalResponse = $cv['response'];//[0]->count_row;
                            $totalResponsePercent = $this->number_format_round($totalResponse * 100 / $totalEvaluate, 2, '.','');
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'เลือก'); 
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $totalResponse); 
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $totalResponsePercent);
                            $row_index++;

                            $totalNonResponse = $totalEvaluate - $totalResponse;//[0]->count_row;
                            $totalNonResponsePercent = $this->number_format_round($totalNonResponse * 100 / $totalEvaluate, 2, '.','');
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'ไม่เลือก'); 
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $totalNonResponse); 
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $totalNonResponsePercent);
                            $row_index++;

                            // Summary
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'รวม');
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, ($totalNonResponse + $totalResponse));
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, ($totalResponsePercent + $totalNonResponsePercent));
                            $row_index++; 
                        }

                    }else{
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, $v['Questions']); 
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "จำนวน"); 
                        $objPHPExcel->getActiveSheet()->getStyle('B' . $row_index)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "คิดเป็นร้อยละ"); 
                        $objPHPExcel->getActiveSheet()->getStyle('C' . $row_index)->getFont()->setBold(true);
                        $objPHPExcel->getActiveSheet()
                            ->getStyle("A".$row_index.":C".$row_index)
                            ->applyFromArray(array(                  
                                    'alignment' => array(
                                        //'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                        'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                                     ),'fill' => array(
                                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'D9DDDE')
                                    )
                                )
                            );
                        $row_index++;

                        // Set details
                        $summary = 0;
                        $summaryPercent = 100;  
                        foreach ($v['ChoiceResponse'] as $ck => $cv) {
                            $summary += floatval($cv['response']);
                        }
                                          
                        foreach ($v['ChoiceResponse'] as $ck => $cv) {
                            // print_r($cv['response']);
                            $totalResponse = $cv['response'];//[0]->count_row;
                            $totalResponsePercent = ($this->number_format_round(($totalResponse / $summary)  * 100, 2, '.',''));
                            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, $cv['ChoiceDesc']); 
                            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $totalResponse); 
                            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $totalResponsePercent);
                            $row_index++; 
                            //$summary += $totalResponse;
                            //$summaryPercent += $totalResponsePercent;
                        }

                        // Summary
                        $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, 'รวม');
                        $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, $summary);
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, $summaryPercent);
                        $row_index++; 
                    }
                }
            }

            $objPHPExcel->getActiveSheet()->getStyle("A3:C" . $objPHPExcel->getActiveSheet()->getHighestRow())
                ->applyFromArray($this->getDefaultStyle());

            return $objPHPExcel;
        }
        
        private function QuerySheet1($objPHPExcel){
            # Divided by System And Manual
            $objPHPExcel->getActiveSheet()->setTitle("สรุปจำนวนผู้ลงทะเบียน");

            $summaryRegister = ReportService::CountRegister();
            $summaryRegisterByType = ReportService::CountRegisterByType();
            $totalRegisterSystem = 0;
            $totalRegisterManual = 0;
            $totalRegisterSystemPercent = 0;
            $totalRegisterManualPercent = 0;
            $row_index = 1;
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, "ประเภทการลงทะเบียน"); 
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "จำนวนคน"); 
            $objPHPExcel->getActiveSheet()->getStyle('B' . $row_index)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "ร้อยละ"); 
            $objPHPExcel->getActiveSheet()->getStyle('C' . $row_index)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()
                ->getStyle("A".$row_index.":C".$row_index)
                ->applyFromArray(array(                  
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                         ),'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '81E3F2')
                        )
                    )
                );
            $row_index++;
            
            foreach($summaryRegisterByType as $k => $v)
            {
                if($v->RegisterType == 'SYSTEM')
                {
                    $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "สรุปจำนวนผู้ลงทะเบียนล่วงหน้า");
                    $totalRegisterSystem = $v->count_row;
                    $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, $totalRegisterSystem);
                    
                    $totalRegisterSystemPercent = number_format($totalRegisterSystem * 100 / $summaryRegister, 2);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, $totalRegisterSystemPercent);
                    
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "สรุปจำนวนผู้ลงทะเบียนหน้างาน");
                    $totalRegisterManual = $v->count_row;
                    $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, $totalRegisterManual);
                    $totalRegisterManualPercent = number_format($totalRegisterManual * 100 / $summaryRegister, 2);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, $totalRegisterManualPercent);
                      
                }
                $row_index++;
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "รวม");
            $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, ($totalRegisterSystem + $totalRegisterManual));
            $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, ($totalRegisterSystemPercent + $totalRegisterManualPercent));
            $objPHPExcel->getActiveSheet()
                ->getStyle("A".$row_index.":C".$row_index)
                ->applyFromArray(array(                  
                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'D9DDDE')
                        )
                    )
                );
            $row_index++;
            $row_index++;

            # Rewards
            $summaryRewards =  ReportService::CountRewards();
            $summaryRewardsbyType =  ReportService::CountRewardsByType();
            $totalRewardsSystem = 0;
            $totalRewardsManual = 0;
            $totalRewardsSystemPercent = 0;
            $totalRewardsManualPercent = 0;

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, "สรุปจำนวนผู้รับของที่ระลึก"); 
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "จำนวนคน"); 
            $objPHPExcel->getActiveSheet()->getStyle('B' . $row_index)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "ร้อยละ"); 
            $objPHPExcel->getActiveSheet()->getStyle('C' . $row_index)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()
                ->getStyle("A".$row_index.":C".$row_index)
                ->applyFromArray(array(                  
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                         ),'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '81E3F2')
                        )
                    )
                );
            $row_index++;
            
            foreach($summaryRewardsbyType as $k => $v)
            {
                if($v->RewardType == 'PREMIUM')
                {
                    $totalRewardsSystem = $v->count_row;
                    $totalRewardsSystemPercent = number_format($totalRewardsSystem * 100 / $summaryRewards, 2);
                    $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "Preium");
                    $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, $totalRewardsSystem);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, $totalRewardsSystemPercent);
                    
                }else{
                    $totalRewardsManual = $v->count_row;
                    $totalRewardsManualPercent = number_format($totalRewardsManual * 100 / $summaryRewards, 2);
                    $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "Normal");
                    $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, $totalRewardsManual);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, $totalRewardsManualPercent);
                }
                $row_index++;
            }
            $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "รวม");
            $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, ($totalRewardsSystem + $totalRewardsManual));
            $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, ($totalRewardsSystemPercent + $totalRewardsManualPercent));
            $objPHPExcel->getActiveSheet()
                ->getStyle("A".$row_index.":C".$row_index)
                ->applyFromArray(array(                  
                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'D9DDDE')
                        )
                    )
                );
            $row_index++;
            $row_index++;

            # Evaluate
            $summaryEvaluate =  count(ReportService::CountEvaluate());
            
            $totalEvaluateSystem = 0;
            $totalEvaluateManual = 0;
            $totalEvaluateSystemPercent = 0;
            $totalEvaluateManualPercent = 0;

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $row_index, "สรุปจำนวนผู้ตอบแบบสอบถาม"); 
            $objPHPExcel->getActiveSheet()->getStyle('A' . $row_index)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $row_index, "จำนวนคน"); 
            $objPHPExcel->getActiveSheet()->getStyle('B' . $row_index)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $row_index, "ร้อยละ"); 
            $objPHPExcel->getActiveSheet()->getStyle('C' . $row_index)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()
                ->getStyle("A".$row_index.":C".$row_index)
                ->applyFromArray(array(                  
                        'alignment' => array(
                            'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP
                         ),'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => '81E3F2')
                        )
                    )
                );
            $row_index++;
            /*
            foreach($summaryEvaluatebyType as $k => $v)
            {
                if($v->RegisterType == 'SYSTEM')
                {
                    $totalEvaluateSystem = $v->count_row;
                    $totalEvaluateSystemPercent = number_format($totalEvaluateSystem * 100 / $summaryEvaluate, 2);
                    $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "สรุปจำนวนผู้ตอบแบบสอบถามผ่านระบบ");
                    $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, $totalEvaluateSystem);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, $totalEvaluateSystemPercent);  
                }else{
                    $totalEvaluateManual = $v->count_row;
                    $totalEvaluateManualPercent = number_format($totalEvaluateManual * 100 / $summaryEvaluate, 2);
                    $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "สรุปจำนวนผู้ตอบแบบสอบถามแบบ Manual");
                    $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, $totalEvaluateManual);
                    $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, $totalEvaluateManualPercent);  
                }
                $row_index++;
            }
            */
            $summaryEvaluatebySystem =  count(ReportService::CountEvaluateBySystem());
            $totalEvaluateSystem = $summaryEvaluatebySystem;
            $totalEvaluateSystemPercent = number_format($totalEvaluateSystem * 100 / $summaryEvaluate, 2);
            $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "สรุปจำนวนผู้ตอบแบบสอบถามผ่านระบบที่ลงทะเบียนล่วงหน้า");
            $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, $totalEvaluateSystem);
            $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, $totalEvaluateSystemPercent);  
            $row_index++;

            $summaryEvaluatebyManual =  count(ReportService::CountEvaluateByManual());
            $totalEvaluateManual = $summaryEvaluatebyManual;
            $totalEvaluateManualPercent = number_format($totalEvaluateManual * 100 / $summaryEvaluate, 2);
            $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "สรุปจำนวนผู้ตอบแบบสอบถามผ่านระบบที่ลงทะเบียนหน้างาน");
            $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, $totalEvaluateManual);
            $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, $totalEvaluateManualPercent);  
            $row_index++;

            $summaryEvaluatebyAdmin =  count(ReportService::CountEvaluateByAdmin());
            $totalEvaluateAdmin = $summaryEvaluatebyAdmin;
            $totalEvaluateAdminPercent = number_format($totalEvaluateAdmin * 100 / $summaryEvaluate, 2);
            $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "สรุปจำนวนผู้ตอบแบบสอบถามแบบ Manual");
            $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, $totalEvaluateAdmin);
            $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, $totalEvaluateAdminPercent);  
            $row_index++;

            $objPHPExcel->getActiveSheet()->setCellValue('A'. $row_index, "รวม");
            $objPHPExcel->getActiveSheet()->setCellValue('B'. $row_index, ($totalEvaluateSystem + $totalEvaluateManual + $totalEvaluateAdmin));
            $objPHPExcel->getActiveSheet()->setCellValue('C'. $row_index, ($totalEvaluateSystemPercent + $totalEvaluateManualPercent + $totalEvaluateAdminPercent));
            
            $objPHPExcel->getActiveSheet()->getStyle("A1:C" . $objPHPExcel->getActiveSheet()->getHighestRow())
                ->applyFromArray($this->getDefaultStyle());
            $objPHPExcel->getActiveSheet()
                ->getStyle("A".$row_index.":C".$row_index)
                ->applyFromArray(array(                  
                        'fill' => array(
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'D9DDDE')
                        )
                    )
                );
            return [$objPHPExcel, ($totalEvaluateSystem + $summaryEvaluatebyManual), $totalEvaluateAdmin];
        }

        private function getReportDate($d){
            $d = explode(' ', $d);
            $date = explode("-", $d[0]);
            $monthTxt = '';
            switch(intval($date[1])){
                case 1 : $monthTxt = 'มกราคม';break;
                case 2 : $monthTxt = 'กุมภาพันธ์';break;
                case 3 : $monthTxt = 'มีนาคม';break;
                case 4 : $monthTxt = 'เมษายน';break;
                case 5 : $monthTxt = 'พฤษภาคม';break;
                case 6 : $monthTxt = 'มิถุนายน';break;
                case 7 : $monthTxt = 'กรกฎาคม';break;
                case 8 : $monthTxt = 'สิงหาคม';break;
                case 9 : $monthTxt = 'กันยายน';break;
                case 10 : $monthTxt = 'ตุลาคม';break;
                case 11 : $monthTxt = 'พฤศจิกายน';break;
                case 12 : $monthTxt = 'ธันวาคม';break;
            }
            return $date[2] . ' ' . $monthTxt . ' ' . ($date[0] + 543);
        }

        private function getReportDateTime($d){
            $dateTime = explode(" ", $d);
            $date = explode("-", $dateTime[0]);
            $monthTxt = '';
            switch(intval($date[1])){
                case 1 : $monthTxt = 'มกราคม';break;
                case 2 : $monthTxt = 'กุมภาพันธ์';break;
                case 3 : $monthTxt = 'มีนาคม';break;
                case 4 : $monthTxt = 'เมษายน';break;
                case 5 : $monthTxt = 'พฤษภาคม';break;
                case 6 : $monthTxt = 'มิถุนายน';break;
                case 7 : $monthTxt = 'กรกฎาคม';break;
                case 8 : $monthTxt = 'สิงหาคม';break;
                case 9 : $monthTxt = 'กันยายน';break;
                case 10 : $monthTxt = 'ตุลาคม';break;
                case 11 : $monthTxt = 'พฤศจิกายน';break;
                case 12 : $monthTxt = 'ธันวาคม';break;
            }
            return $date[2] . ' ' . $monthTxt . ' ' . ($date[0] + 543) . ' ' . $dateTime[1];
        }

        private function calculateFiscalYear($year){
            $startYear = $year - 1; 
            $endYear = $year;

            $startDate = $startYear . '-10-01 00:00:00.000';
            $endDate = $endYear . '-09-30 23:59:59.000';
            return ['startDate' => $startDate, 'endDate' => $endDate];
        }

        private function getMaxDayInmonth($dateStr){
            return date("t", strtotime($dateStr));
        }

        private function getDefaultStyle(){
            return array(                  
                    'borders' => array(
                        'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                        )
                    ),
                    // 'alignment' => array(
                    //     'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    //     'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
                    //     'wrap' => true
                    //  ),
                     'font'  => array(
                        'size'  => 11,
                        // 'bold'  => true
                    )
                );
        }

        private function number_format_round($float, $fracCnt=2, $decimalDot='.', $thousendSeperator = ',', $precision=6){
            $float = (string)(empty($float)?'0.00':$float);
            list($n, $frac) = explode('.', $float);
            
            $firstIndex = '';
            if((string) $n === '-0'){
                echo $firstIndex = '-';
            }
            
            $n = (int)$n;
            
            $fracPre = substr($frac, 0, $precision);
            $fracPre = (int)str_pad($fracPre, $precision, '0');
            $fracLim = substr($frac, 0, $fracCnt);
            $fracLim = (int)str_pad($fracLim, $precision, '0');
            $fracPreLen = strlen($fracPre);
            $prefixCnt = 0;
            if ($fracPreLen < $precision){      
                if (!empty($fracPre)){
                    $prefixCnt = $precision - strlen($fracPre);
                    if ($prefixCnt > $fracCnt){
                        $prefixCnt = $fracCnt;
                    }
                }
            }
            $half = pow(10, ($precision - $fracCnt - 1)) * 5;   
            $fracLimHalf = $fracLim + $half;
            if ($fracPre >= $fracLimHalf){
                $fracRound = $fracLimHalf + $half;
                if (strlen($fracRound) > $fracPreLen){
                    if (strlen($fracRound) > $precision){
                        $n += ($n > 0) ? 1 : -1;
                        $fracRound = 0;
                        $prefixCnt = 0;
                    } else {
                        $prefixCnt--;
                        if ($prefixCnt < 0 ){
                            $prefixCnt = 0;
                        }
                    }
                }
            }
            else{
                $fracRound = $fracLim;
            }
            $fracRoundLen = $fracCnt - $prefixCnt;
            $fracRound = substr($fracRound, 0, $fracRoundLen);
            $fracRound = str_pad($fracRound, $fracRoundLen, '0');
            
            return number_format(($firstIndex . $n . '.' . str_repeat('0',$prefixCnt) . $fracRound), $fracCnt, $decimalDot, $thousendSeperator);
        }
    }

?>