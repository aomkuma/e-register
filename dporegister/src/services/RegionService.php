<?php

	namespace App\Service;

	use App\Model\Province;
	use App\Model\Amphur;
    use App\Model\District;

    use Illuminate\Database\Capsule\Manager as DB;

	class RegionService {

        public static function getProvinceList(){
            return Province::with(['amphurList' => function ($query) {
                        $query->with('districtList');
                     }])
            		->get();
        }

	}