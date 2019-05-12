<?php

	namespace App\Controller;
    
    use App\Service\QuestionService;
    
    class QuestionController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
        }

        
        public function getChoiceIconList($request, $response, $args){
            
            try{
                $obj = $request->getParsedBody();
                // $question_year_id = $obj['obj']['question_year_id'];

                $DataList = QuestionService::getChoiceIconList();
                $this->data_result['DATA']['List'] = $DataList;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function getQuestionDetailList($request, $response, $args){
            
            try{
                $obj = $request->getParsedBody();
                $question_year_id = $obj['obj']['question_year_id'];

                $DataList = QuestionService::getQuestionDetailList($question_year_id);
                $this->data_result['DATA']['List'] = $DataList;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function getQuestionDataList($request, $response, $args){
            
            try{
                $obj = $request->getParsedBody();
                $question_section_id = $obj['obj']['question_section_id'];
                $question_year_id = $obj['obj']['question_year_id'];

                $DataList = QuestionService::getQuestionDataList($question_section_id, $question_year_id);
                $this->data_result['DATA']['List'] = $DataList;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function getQuestionSection($request, $response, $args){
            
            try{
                $obj = $request->getParsedBody();
                $id = $obj['obj']['id'];

                $Data = QuestionService::getQuestionSection($id);
                $this->data_result['DATA'] = $Data;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function getQuestionYearList($request, $response, $args){
            //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');

            try{
                $DataList = QuestionService::getQuestionYearList();
                $this->data_result['DATA']['List'] = $DataList;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function getQuestionYear($request, $response, $args){
            //         error_reporting(E_ERROR);
    // error_reporting(E_ALL);
    // ini_set('display_errors','On');

            try{
                $obj = $request->getParsedBody();
                $id = $obj['obj']['question_year_id'];

                $Data = QuestionService::getQuestionYear($id);
                $this->data_result['DATA'] = $Data;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function updateQuestionYear($request, $response, $args){
            
            try{

                $obj = $request->getParsedBody();
                $Data = $obj['obj']['Data'];

                $id = QuestionService::updateQuestionYear($Data);
                $this->data_result['DATA']['id'] = $id;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function updateQuestionSection($request, $response, $args){
            
            try{

                $obj = $request->getParsedBody();
                $Data = $obj['obj']['Data'];

                $id = QuestionService::updateQuestionSection($Data);
                $this->data_result['DATA']['id'] = $id;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function updateQuestionData($request, $response, $args){
            ini_set('max_input_vars','2000' );
            try{

                $obj = $request->getParsedBody();
                $Data = $obj['obj']['Data'];

                
                $files = $request->getUploadedFiles();
                // print_r($files);
                // exit;
                $cnt = 0;
                foreach ($Data as $key => $value) {

                    foreach ($value as $k => $v) {
                        if($v == 'null'){
                            $value[$k] = NULL;
                        }
                    }

                    // print_r($files['obj']['Data'][$cnt]['AttachFile']);
                    $cur_file = $files['obj']['Data'][$cnt]['AttachFile'];
                    if(!empty($cur_file)){
                        // echo "$cnt has file\n";
                        $ext = pathinfo($cur_file->getClientFilename(), PATHINFO_EXTENSION);
                        $FileName = date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                        $FilePath = 'img/'.$FileName;

                        $value['background_img'] = $FileName;

                        $cur_file->moveTo('../../' . $FilePath);
                    }
                    
                    $ChoiceList = $value['choice_list'];
                    unset($value['choice_list']);
                    $question_id = QuestionService::updateQuestionData($value);    

                    foreach ($ChoiceList as $_key => $_value) {
                        foreach ($_value as $_k => $_v) {
                            if($_v == 'null'){
                                $_value[$_k] = NULL;
                            }
                        }
                        $_value['QuestionID'] = $question_id;
                        $choice_id = QuestionService::updateChoiceData($_value);    
                    }
                    $cnt++;
                }
                // exit;
                
                $this->data_result['DATA']['id'] = $question_id;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function updateIcon($request, $response, $args){
            
            try{

                $obj = $request->getParsedBody();
                $Data = $obj['obj']['Data'];

                $choice_icon_detail = $Data['choice_icon_detail'];
                unset($Data['choice_icon_detail']);

                $icon_id = QuestionService::updateIcon($Data);    
                
                $files = $request->getUploadedFiles();
                // print_r($files);
                // exit;
                $cnt = 0;
                foreach ($choice_icon_detail as $key => $value) {

                    foreach ($value as $_k => $_v) {
                        if($_v == 'null'){
                            $value[$_k] = NULL;
                        }
                    }

                    $cur_file = $files['obj']['Data']['choice_icon_detail'][$cnt]['AttachFile'];
                    if(!empty($cur_file)){
                        // echo "$cnt has file\n";
                        $ext = pathinfo($cur_file->getClientFilename(), PATHINFO_EXTENSION);
                        $FileName = date('YmdHis').'_'.rand(100000,999999). '.'.$ext;
                        $FilePath = 'img/'.$FileName;

                        $value['img_path'] = $FileName;

                        $cur_file->moveTo('../../' . $FilePath);
                    }
                    
                    $value['icon_id'] = $icon_id;
                    $icon_detail_id = QuestionService::updateIconDetail($value);    
                    
                    $cnt++;
                }
                // exit;
                
                $this->data_result['DATA']['id'] = $question_id;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function deleteQuestionData($request, $response, $args){
            
            try{

                $obj = $request->getParsedBody();
                $QuestionID = $obj['obj']['QuestionID'];

                $result = QuestionService::removeQuestionData($QuestionID);
                $this->data_result['DATA']['result'] = $result;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

        public function deleteIconDetail($request, $response, $args){
            
            try{

                $obj = $request->getParsedBody();
                $id = $obj['obj']['id'];

                $result = QuestionService::removeIconDetail($id);
                $this->data_result['DATA']['result'] = $result;

                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }

    }