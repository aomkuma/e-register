<?php

    namespace App\Controller;
    
    use App\Service\RegionService;
    use App\Controller\SMS;

    class RegionController extends Controller {
        
        protected $logger;
        protected $db;
        
        public function __construct($logger, $db){
            $this->logger = $logger;
            $this->db = $db;
            
        }
        public function getProvince($request, $response, $args){
            try{
            
                $ProvinceList = RegionService::getProvinceList();
                $this->data_result['DATA'] = $ProvinceList;
                return $this->returnResponse(200, $this->data_result, $response, false);
                
            }catch(\Exception $e){
                return $this->returnSystemErrorResponse($this->logger, $this->data_result, $e, $response);
            }
        }
    }