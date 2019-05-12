app.controller('AppController', ['$cookies','$scope', '$filter', '$uibModal','IndexOverlayFactory', function($cookies, $scope, $filter, $uibModal, IndexOverlayFactory) {
	$scope.overlay = IndexOverlayFactory;
	$scope.overlayShow = false;
	$scope.currentUser = null;
    $scope.TotalLogin = 0;
    $scope.menu_selected = '';

    
    $scope.logout = function(){
        sessionStorage.removeItem('user_session');
        sessionStorage.removeItem('user_authen_session');
        //sessionStorage.removeItem('MenuList');
        $scope.currentUser = null;
        window.location.replace('#/register');
    }

    $scope.logoutAdmin = function(){
        sessionStorage.removeItem('user_session');
        
        //sessionStorage.removeItem('MenuList');
        $scope.currentUser = null;
        window.location.replace('#/logon');
    }

    $scope.filterMenu = function(MenuID){
        //console.log('MenuID = ', MenuID);
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        var result = false;
        if(MenuID == '5' || MenuID == '6'){
            result = $filter('MenuFilter')($scope.MenuList, 6);     
            if(!result){
                result = $filter('MenuFilter')($scope.MenuList, 5);    
                return result;
            }else{
                return result;
            }
            
        }else{
            result = $filter('MenuFilter')($scope.MenuList, MenuID);
            return result;
        }
        
        
    }

    $scope.filterHeadMenu = function(MenuIDList){
        //console.log('MenuID = ', MenuID);
        var result = false;
        $scope.MenuList = angular.fromJson(sessionStorage.getItem('MenuList'));
        if(MenuIDList != ''){
            var menuIdList = MenuIDList.split(',');
            for(var i = 0; i < menuIdList.length; i++){
                result = $filter('MenuFilter')($scope.MenuList, menuIdList[i]);   
                if(result){
                    return result;
                }
            }
        }else{
            result = $filter('MenuFilter')($scope.MenuList, 1);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 2);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 3);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 4);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 5);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 6);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 7);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 8);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 9);   
            if(result){
                return result;
            }
            result = $filter('MenuFilter')($scope.MenuList, 10);   
            if(result){
                return result;
            }
        }

        return result;
    }
    //console.log('AppController ',$scope.currentUser);
}]);


app.controller('HomeController', function($scope, $location, $sce, IndexOverlayFactory) {
	//sessionStorage.removeItem('user_session');
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = '';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        console.log($scope.$parent.currentUser);
        console.log($scope.$parent.currentUser.Firstname);
        
    }else{
       window.location.replace('#/register');
    }
    IndexOverlayFactory.overlayHide();
});

app.controller('HomePageController', function($scope, $location, $sce, IndexOverlayFactory) {
    //sessionStorage.removeItem('user_session');
    
});

app.controller('IntroController', function($scope, $location, $sce, IndexOverlayFactory) {
    //sessionStorage.removeItem('user_session');
    
});

app.controller('HomeAdminController', function($scope, $location, $sce, IndexOverlayFactory) {
    //sessionStorage.removeItem('user_session');
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = '';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        console.log($scope.$parent.currentUser);
        console.log($scope.$parent.currentUser.Firstname);
        
    }else{
       window.location.replace('#/logon');
    }
    IndexOverlayFactory.overlayHide();
});

app.controller('AttendeeListController', function($scope, $uibModal, $location, $sce, HTTPService, IndexOverlayFactory) {
    //sessionStorage.removeItem('user_session');
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = 'attendee-list';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        // console.log($scope.$parent.currentUser);
        // console.log($scope.$parent.currentUser.Firstname);
        
    }else{
       window.location.replace('#/logon');
    }

    if($scope.currentUser.AdminType != 'SUPERS' && $scope.currentUser.AdminType != 'NORMAL'){
        alert('คุณไม่มีสิทธิ์ใช้งานหน้านี้');
        window.location.replace('#/logon');
        return false;
    }

    IndexOverlayFactory.overlayHide();

    $scope.loadData = function(condition){
        if($scope.continueLoad){
            $scope.tableLoad = true;
            var params = {'keyword':condition.keyword, 'registerType':condition.registerType, 'years':$scope.condition.years, 'offset':$scope.dataOffset};
            HTTPService.clientRequest('attendeeList', params).then(function (result) {
                if(result.data.STATUS == 'OK'){
                    $scope.tableLoad = false;
                    $scope.dataOffset =  result.data.DATA.offset;
                    $scope.continueLoad = result.data.DATA.continueLoad;
                    $scope.totalData = result.data.DATA.totalData;
                    for(var i = 0; i < result.data.DATA.DataList.length; i++){
                        $scope.DataList.push(result.data.DATA.DataList[i]);    
                    }
                }
                $scope.DisabledSearch = false;
            });
        }
    }

    $scope.search = function(condition){
        $scope.DisabledSearch = true;
        $scope.dataOffset = 0;
        $scope.tableLoad = false;
        $scope.continueLoad = true;
        $scope.DataList = [];
        $scope.loadData(condition);
    }

    $scope.confirmChangeEvaluate = function(UserID, Evaluate, index){
        var RecieveEvaluate = '';
        if(Evaluate == 'Y'){
            RecieveEvaluate = 'เลือกทำแบบประเมิน';
        }else{
            RecieveEvaluate = 'ยกเลิกทำแบบประเมิน';
        }
        $scope.alertMessage = 'ต้องการ' + RecieveEvaluate + 'สำหรับบุคคลนี้ ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : true,
                templateUrl : 'html/dialog_confirm.html',
                size : 'sm',
                scope : $scope,
                backdrop : 'static',
                controller : 'ModalDialogPreventCtrl',
                resolve : {
                    params : function() {
                        return {};
                    } 
                },
            });

            modalInstance.result.then(function (valResult) {
                if(valResult == 'OK'){
                    var params = {'UserID':UserID, 'Evaluate':Evaluate};
                    IndexOverlayFactory.overlayShow();
                    HTTPService.clientRequest('updateEvaluate', params).then(function (result) {
                        IndexOverlayFactory.overlayHide();
                        if(result.data.STATUS == 'ERROR'){
                            alert(result.data.DATA);
                        }
                    });
                }else{
                    if(Evaluate == 'Y'){
                        $scope.DataList[index].Evaluate = '';
                    }else{
                        $scope.DataList[index].Evaluate = 'Y';
                    }
                }
            });
    }

    $scope.confirmChangeRewards = function(id, Rewards, index){
        var RecieveRewards = '';
        if(Rewards == 'Y'){
            RecieveRewards = 'เลือกรับของรางวัล';
        }else{
            RecieveRewards = 'ยกเลิกการรับของรางวัล';
        }
        $scope.alertMessage = 'ต้องการ' + RecieveRewards + 'สำหรับบุคคลนี้ ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : true,
                templateUrl : 'html/dialog_confirm.html',
                size : 'sm',
                scope : $scope,
                backdrop : 'static',
                controller : 'ModalDialogPreventCtrl',
                resolve : {
                    params : function() {
                        return {};
                    } 
                },
            });

            modalInstance.result.then(function (valResult) {
                if(valResult == 'OK'){
                    var params = {'id':id, 'Rewards':Rewards};
                    IndexOverlayFactory.overlayShow();
                    HTTPService.clientRequest('updateRewards', params).then(function (result) {
                        IndexOverlayFactory.overlayHide();
                        if(result.data.STATUS == 'ERROR'){
                            alert(result.data.DATA);
                        }else{
                            $scope.DataList[index].RewardType = result.data.DATA.RewardType;
                            $scope.DataList[index].RewardDate = result.data.DATA.RewardDate;
                        }
                    });
                }else{
                    if(Rewards == 'Y'){
                        $scope.DataList[index].Rewards = '';
                    }else{
                        $scope.DataList[index].Rewards = 'Y';
                    }
                }
            });
    }

    $scope.viewProfile = function (index, data){
        $scope.Contact = data;
        var RewardType = data.RewardType;
        $scope.DataIndex = index;
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'view_profile.html',
            size: 'md',
            scope: $scope,
            backdrop: 'static',
            controller: 'ModalDialogReturnFromOKBtnCtrl',
            resolve: {
                params: function () {
                    return {};
                }
            },
        });

        modalInstance.result.then(function (valResult) {
            console.log(valResult);
            if(valResult.RewardType != RewardType){
                var params = {'id':valResult.id, 'RewardType':valResult.RewardType};
                HTTPService.clientRequest('updateRewardType', params).then(function (result) {
                    if(result.data.STATUS == 'OK'){
                        $scope.DataList[index].RewardType = valResult.RewardType;
                    }
                });
            }
        });

    }

    $scope.goEvaluateAdmin = function(UserID, type){
        if(type == 'GOOLDER'){
            // sessionStorage.setItem('user_authen_session' , JSON.stringify($scope.Attendee));
            window.location.href = '#/evaluate-admin/' + UserID;
        }else if(type == 'GOYOUNGER'){
            // sessionStorage.setItem('user_authen_session' , JSON.stringify($scope.Attendee));
            window.location.href = '#/evaluate-admin/' + UserID + '/ALL';
        }
    }

    $scope.convertDate = function(date){
        return convertDateToFullThaiDateIgnoreTime(new Date(date));
    }

    $scope.DisabledSearch = false;
    $scope.dataOffset = 0;
    $scope.tableLoad = false;
    $scope.continueLoad = true;
    $scope.DataList = [];
    $scope.condition = {'keyword':'', 'registerType':'', 'years' : '2019'};
    $scope.loadData($scope.condition);

});

app.controller('LoginController',function($scope, $routeParams, $filter, HTTPService, IndexOverlayFactory){
	
    $scope.showLogin = false;
    
	$scope.user = {'Username':'','Password':''};

    var reDirect = '';
    if($routeParams.redirect_url !== undefined){
        reDirect = $routeParams.redirect_url;
        console.log(reDirect);
    }
	$scope.showError = false; // set Error flag
	$scope.showSuccess = false; // set Success Flag
    $scope.showUserPass = false;
    IndexOverlayFactory.overlayHide();
	//------- Authenticate function
	$scope.authenticateAdmin = function (confirm, event){
        
        if(confirm=='login' || event.keyCode == '13'){
            if($scope.user.Username != '' && $scope.user.Username != null && $scope.user.Password != '' && $scope.user.Password != null){
        		var flag= false;
                $scope.showUserPass = false;
                $scope.showError = false;
                $scope.showSuccess = false;
                IndexOverlayFactory.overlayShow();
        		HTTPService.clientRequest('loginAdmin', $scope.user).then(function (user) {
                    IndexOverlayFactory.overlayHide();
            		//-------- set error or success flags
            		if(user.data.STATUS == 'OK'){
            			$scope.showError = false;
                        $scope.showUserPass = false;
            			$scope.showSuccess = true;
                        sessionStorage.setItem('user_session' , JSON.stringify(user.data.DATA.UserData));
                        sessionStorage.setItem('MenuList' , JSON.stringify(user.data.DATA.MenuList));
                        setTimeout(function(){
                            if(reDirect == '' || reDirect == null){
                                reDirect = 'home-admin';
                            }
                            window.location.replace('#/' + reDirect);    
                        }, 1000);
                        
            		}
            		else{
                        $scope.errorMsg = user.data.DATA; //'Invalid username or password';
            			$scope.showError = true;
            			$scope.showSuccess = false;
            		}
                    
                });
            }else{
                $scope.showUserPass = true;
            }
        }
	}

});

app.controller('RegisterController',function($scope, $routeParams, $filter, HTTPService, IndexOverlayFactory){
    
    $scope.registerMember = function(register){
        $scope.showRegisUserPass = false;
        $scope.showRegisError = false;
        $scope.showRegisSuccess = false;
        IndexOverlayFactory.overlayShow();
        HTTPService.clientRequest('register', register).then(function (res) {
            IndexOverlayFactory.overlayHide();
            if(res.data.STATUS == 'OK'){
                window.location.replace('#/register-success');    
               
            }else{
                $scope.errorMsg = res.data.DATA; //'Invalid username or password';
                $scope.showRegisError = true;
                $scope.showRegisSuccess = false;
                $scope.RegisSuccess = false;
            }
        });
    }

    $scope.getProvinceList = function(){
        HTTPService.clientRequest('getProvince', []).then(function (res) {
            IndexOverlayFactory.overlayHide();
            if(res.data.STATUS == 'OK'){
                $scope.ProvinceList = res.data.DATA;
            }
        });   
    }

    $scope.setProvince = function(provinceID){
        var index = $filter('FindProvinceID')($scope.ProvinceList, provinceID);  
        if(index != -1)
        {
            $scope.register.Province = $scope.ProvinceList[index].PROVINCE_NAME;
            console.log($scope.register.Province);
            $scope.AmphurList = $scope.ProvinceList[index].amphur_list;
            $scope.DistrictList = [];
        }
    }

    $scope.setAmphur = function(amphurID){
        var index = $filter('FindAmphurID')($scope.AmphurList, amphurID);  
        console.log(index);
        if(index != -1)
        {
            $scope.register.SubProvince = $scope.AmphurList[index].AMPHUR_NAME;
            console.log($scope.register.SubProvince, $scope.AmphurList[index]);
            $scope.DistrictList = $scope.AmphurList[index].district_list;
        }
    }

    $scope.setDistrict = function(districtID){
        var index = $filter('FindDistrictID')($scope.DistrictList, districtID);  
        console.log(index);
        if(index != -1)
        {
            $scope.register.District = $scope.DistrictList[index].DISTRICT_NAME;
            console.log($scope.register.District);
        }
    }

    $scope.changeMonth = function(month, year){
        $scope.DateList = getTotalDayInMonth(month, year);
    }

    $scope.YearList = getYearOfBirthList();
    $scope.MonthList = getThaiMonth();
    $scope.DateList = getTotalDayInMonth(null, null);
    $scope.ProvinceList = $scope.getProvinceList();
    $scope.showRegister = false;
    $scope.RegisSuccess = false;


});

app.controller('RegistrationController',function($scope, $routeParams, $filter, HTTPService, IndexOverlayFactory){
    sessionStorage.removeItem('user_session');
    sessionStorage.removeItem('user_authen_session');

    $scope.registerMember = function(register){
        $scope.showRegisUserPass = false;
        $scope.showRegisError = false;
        $scope.showRegisSuccess = false;
        IndexOverlayFactory.overlayShow();
        HTTPService.clientRequest('registration', register).then(function (res) {
            IndexOverlayFactory.overlayHide();
            if(res.data.STATUS == 'OK'){
                //window.location.replace('#/register-success');    
               console.log(res.data.UserData);
               sessionStorage.setItem('user_authen_session' , JSON.stringify(res.data.UserData));
                window.location.href = '#/evaluate';
            }else{
                $scope.errorMsg = res.data.DATA; //'Invalid username or password';
                $scope.showRegisError = true;
                $scope.showRegisSuccess = false;
                $scope.RegisSuccess = false;
            }
        });
    }

    $scope.getProvinceList = function(province, subProvince, district){
        HTTPService.clientRequest('getProvince', []).then(function (res) {
            IndexOverlayFactory.overlayHide();
            if(res.data.STATUS == 'OK'){
                $scope.ProvinceList = res.data.DATA;
                // Find province id
                $scope.register.ProvinceID = $filter('FindProvinceName')($scope.ProvinceList, $scope.register.Province);  
                $scope.setProvince($scope.register.ProvinceID);

                if(province != null){
                    $scope.setProvince(province);
                }
                if(subProvince != null){
                    $scope.setAmphur(subProvince);
                }
                if(district != null){
                    $scope.setDistrict(district);
                }
            }
        });   
    }

    $scope.setProvince = function(provinceID){
        var index = $filter('FindProvinceID')($scope.ProvinceList, provinceID);  
        if(index != -1)
        {
            $scope.register.Province = $scope.ProvinceList[index].PROVINCE_NAME;
            console.log($scope.register.Province);
            $scope.AmphurList = $scope.ProvinceList[index].amphur_list;
            $scope.DistrictList = [];

            // Find Amphur
            $scope.register.SubProvinceID = $filter('FindAmphurName')($scope.AmphurList, $scope.register.SubProvince);  
            console.log($scope.register.SubProvinceID);
            $scope.setAmphur($scope.register.SubProvinceID);
        }
    }

    $scope.setAmphur = function(amphurID){
        var index = $filter('FindAmphurID')($scope.AmphurList, amphurID);  
        console.log(index);
        if(index != -1)
        {
            $scope.register.SubProvince = $scope.AmphurList[index].AMPHUR_NAME;
            console.log($scope.register.SubProvince, $scope.AmphurList[index]);
            $scope.DistrictList = $scope.AmphurList[index].district_list;

            // find district
            $scope.register.DistrictID = $filter('FindDistrictName')($scope.DistrictList, $scope.register.District);  
        }
    }

    $scope.setDistrict = function(districtID){
        var index = $filter('FindDistrictID')($scope.DistrictList, districtID);  
        console.log(index);
        if(index != -1)
        {
            $scope.register.District = $scope.DistrictList[index].DISTRICT_NAME;
            console.log($scope.register.District);
        }
    }

    $scope.changeMonth = function(month, year){
        $scope.DateList = getTotalDayInMonth(month, year);
    }

    $scope.checkIDCardProfile = function(idcard){
        console.log(idcard);
        IndexOverlayFactory.overlayShow();
        HTTPService.clientRequest('findWithIDCard', {'IDCard' : idcard}).then(function (res) {
            IndexOverlayFactory.overlayHide();
            if(res.data.STATUS == 'OK'){
                $scope.register = res.data.DATA.UserData;
                var bt = $scope.register.BirthDate.split('/');
                $scope.register.YearOfBirth = parseInt(bt[2]);
                $scope.register.MonthOfBirth = (bt[1]);
                $scope.register.DateOfBirth = (bt[0]);
                console.log($scope.register.ProvinceID);
                $scope.register.ProvinceID = parseInt($scope.register.ProvinceID);
                $scope.ProvinceList = $scope.getProvinceList($scope.register.ProvinceID, $scope.register.SubProvinceID, $scope.register.DistrictID);
                // $scope.setProvince($scope.register.ProvinceID);
                // $scope.setAmphur($scope.register.SubProvinceID);
                // $scope.setDistrict($scope.register.DistrictID);
                // $scope.YearList = getYearOfBirthList();
            }
            IndexOverlayFactory.overlayHide();
        });   
    }

    $scope.YearList = getYearOfBirthList();
    $scope.MonthList = getThaiMonth();
    $scope.DateList = getTotalDayInMonth(null, null);
    $scope.ProvinceList = $scope.getProvinceList();
    $scope.showRegister = false;
    $scope.RegisSuccess = false;
    $scope.register = {UserID:'',IDCard:'',Firstname:'',Lastname:'',Gender:'',DateOfBirth:'',MonthOfBirth:''
                        ,YearOfBirth:'',AddressNo:'',Moo:'',District:'',SubProvince:'',Province:''};
    setTimeout(function(){
        if($routeParams.IDCard != ''){
            $scope.register.IDCard = $routeParams.IDCard;
            $scope.checkIDCardProfile($scope.register.IDCard);

        }
        if($routeParams.smartcard != undefined && $routeParams.smartcard != null && $routeParams.smartcard != ''){
            $scope.smartcard = angular.fromJson(b64DecodeUnicode($routeParams.smartcard.replace(' ', '+')));
            $scope.register.IDCard = $scope.smartcard.CitizenID;
            $scope.register.Firstname = $scope.smartcard.NameTH_FirstName;
            $scope.register.Lastname = $scope.smartcard.NameTH_SurName;
            $scope.register.Gender = ($scope.smartcard.Sex=='ชาย'?'M':'F');
            $scope.register.AddressNo = $scope.smartcard.Address_No;
            $scope.register.Moo = $scope.smartcard.Address_Moo;
            $scope.register.District = $scope.smartcard.Address_Tumbol + '   ';
            $scope.register.SubProvince = $scope.smartcard.Address_Amphur + '   ';
            $scope.register.Province = $scope.smartcard.Address_Province + '   ';



            // console.log($scope.smartcard);
            var birthdateArr = $scope.smartcard.BirthDate.split(" ");
            var birthdate1 = birthdateArr[0].split('/');
            var bdate = birthdate1[0];//(birthdate1[1].length==1?'0'+birthdate1[1]:birthdate1[1]);
            var bmonth = birthdate1[1];//(birthdate1[0].length==1?'0'+birthdate1[0]:birthdate1[0]);
            var byear = birthdate1[2];//parseInt(birthdate1[2]) +543;
            $scope.register.DateOfBirth = parseInt(bdate);
            $scope.register.MonthOfBirth = bmonth;
            $scope.register.YearOfBirth = parseInt(birthdate1[2]) -  543;
            console.log($scope.register);
        }
    },200);
    

});

app.controller('PreRegisterController',function($scope, $routeParams, $filter, HTTPService, IndexOverlayFactory){
    
    $scope.registerMember = function(register){
        $scope.showRegisUserPass = false;
        $scope.showRegisError = false;
        $scope.showRegisSuccess = false;
        IndexOverlayFactory.overlayShow();
        HTTPService.clientRequest('register', register).then(function (res) {
            IndexOverlayFactory.overlayHide();
            if(res.data.STATUS == 'OK'){
                window.location.replace('#/register-success');    
               
            }else{
                $scope.errorMsg = res.data.DATA; //'Invalid username or password';
                $scope.showRegisError = true;
                $scope.showRegisSuccess = false;
                $scope.RegisSuccess = false;
            }
        });
    }

    $scope.getProvinceList = function(province, subProvince, district){
        console.log(province, subProvince, district);
        HTTPService.clientRequest('getProvince', []).then(function (res) {
            IndexOverlayFactory.overlayHide();
            if(res.data.STATUS == 'OK'){
                $scope.ProvinceList = res.data.DATA;
                if(province != null){
                    $scope.setProvince(province);
                }
                if(subProvince != null){
                    $scope.setAmphur(subProvince);
                }
                if(district != null){
                    $scope.setDistrict(district);
                }
            }
        });   
    }

    $scope.setProvince = function(provinceID){
        var index = $filter('FindProvinceID')($scope.ProvinceList, provinceID);  
        if(index != -1)
        {
            $scope.register.Province = $scope.ProvinceList[index].PROVINCE_NAME;
            console.log($scope.register.Province);
            $scope.AmphurList = $scope.ProvinceList[index].amphur_list;
            $scope.DistrictList = [];
        }
    }

    $scope.setAmphur = function(amphurID){
        var index = $filter('FindAmphurID')($scope.AmphurList, amphurID);  
        console.log(index);
        if(index != -1)
        {
            $scope.register.SubProvince = $scope.AmphurList[index].AMPHUR_NAME;
            console.log($scope.register.SubProvince, $scope.AmphurList[index]);
            $scope.DistrictList = $scope.AmphurList[index].district_list;
        }
    }

    $scope.setDistrict = function(districtID){
        var index = $filter('FindDistrictID')($scope.DistrictList, districtID);  
        console.log(index);
        if(index != -1)
        {
            $scope.register.District = $scope.DistrictList[index].DISTRICT_NAME;
            console.log($scope.register.District);
        }
    }

    $scope.changeMonth = function(month, year){
        $scope.DateList = getTotalDayInMonth(month, year);
    }

    $scope.checkIDCardProfile = function(idcard){
        console.log(idcard);
        IndexOverlayFactory.overlayShow();
        HTTPService.clientRequest('findWithIDCard', {'IDCard' : idcard}).then(function (res) {
            IndexOverlayFactory.overlayHide();
            if(res.data.STATUS == 'OK'){
                $scope.register = res.data.DATA.UserData;
                var bt = $scope.register.BirthDate.split('/');
                $scope.register.YearOfBirth = parseInt(bt[2]);
                $scope.register.MonthOfBirth = (bt[1]);
                $scope.register.DateOfBirth = (bt[0]);
                console.log($scope.register.ProvinceID);
                $scope.register.ProvinceID = parseInt($scope.register.ProvinceID);
                $scope.ProvinceList = $scope.getProvinceList($scope.register.ProvinceID, $scope.register.SubProvinceID, $scope.register.DistrictID);
                // $scope.YearList = getYearOfBirthList();
            }
            IndexOverlayFactory.overlayHide();
        });   
    }

    $scope.YearList = getYearOfBirthList();
    $scope.MonthList = getThaiMonth();
    $scope.DateList = getTotalDayInMonth(null, null);
    $scope.ProvinceList = $scope.getProvinceList(null, null, null);
    $scope.showRegister = false;
    $scope.RegisSuccess = false;


});

app.controller('SmartcardRegisterController',function($scope, $routeParams, $filter, HTTPService, IndexOverlayFactory){
    
    $scope.gotoEvaluate = function(smartcard){
        sessionStorage.setItem('user_authen_session' , JSON.stringify($scope.register));
        window.location.href = '#/evaluate';
    }

    $scope.gotoRegister = function(){
        window.location.href = '#/register';
    }
    $scope.ShowSmartCard = false;
    $scope.PermissionPass = false;
    $scope.Evaluated = false;

    if($routeParams.smartcard != undefined && $routeParams.smartcard != null){
        $scope.ShowSmartCard = true;
        
        $scope.smartcard = angular.fromJson(b64DecodeUnicode($routeParams.smartcard.replace(' ', '+')));
        console.log($scope.smartcard);
        var birthdateArr = $scope.smartcard.BirthDate.split(" ");
        var birthdate1 = birthdateArr[0].split('/');
        var bdate = birthdate1[0];//(birthdate1[1].length==1?'0'+birthdate1[1]:birthdate1[1]);
        var bmonth = birthdate1[1];//(birthdate1[0].length==1?'0'+birthdate1[0]:birthdate1[0]);
        var byear = birthdate1[2];//parseInt(birthdate1[2]) +543;
        $scope.smartcard.BirthDate = bdate + '/' + bmonth + '/' + byear;
        
        var birthdateArr = $scope.smartcard.BirthDate.split(" ");
        var birthdate1 = birthdateArr[0].split('/');

        $scope.register = {UserID:'',IDCard:'',Firstname:'',Lastname:'',Gender:'',DateOfBirth:'',MonthOfBirth:''
                        ,YearOfBirth:'',AddressNo:'',Moo:'',District:'',SubProvince:'',Province:''};

        $scope.register.IDCard = $scope.smartcard.CitizenID;
        $scope.register.Firstname = $scope.smartcard.NameTH_FirstName;
        $scope.register.Lastname = $scope.smartcard.NameTH_SurName;
        $scope.register.Gender = ($scope.smartcard.Sex=='ชาย'?'M':'F');
        $scope.register.DateOfBirth = bdate;
        $scope.register.MonthOfBirth = bmonth;
        $scope.register.YearOfBirth = parseInt(birthdate1[2]);
        $scope.register.AddressNo = $scope.smartcard.Address_No;
        $scope.register.Moo = $scope.smartcard.Address_Moo;
        $scope.register.District = $scope.smartcard.Address_Tumbol;
        $scope.register.SubProvince = $scope.smartcard.Address_Amphur;
        $scope.register.Province = $scope.smartcard.Address_Province + '   ';
        
        
        if($routeParams.registype != 'nonidcard'){
            HTTPService.clientRequest('findRegisteredWithIDCard',{'IDCard':$scope.register.IDCard}).then(function(result){
                if(result.data.STATUS == 'OK'){
                    $scope.LoginSmartCard = result.data.DATA.UserData;
                    $scope.register.UserID = result.data.DATA.UserData.UserID;
                    $scope.PermissionPass = true;
                    if(result.data.DATA.UserData.Evaluate == 'Y'){
                        $scope.Evaluated = true;
                    }
                }else{
                    //alert('ไม่พบรายชื่อผู้ลงทะเบียนจากหมายเลขบัตร ' + $scope.register.IDCard + ' กรุณาทำการลงทะเบียน'); //ติดต่อเจ้าหน้าที่เพื่อลงทะเบียนหน้างาน
                    window.location.replace("#/registration/" + $scope.register.IDCard + "/" + $routeParams.smartcard);

                }
            });   
        }else{
            console.log('pass');
            $scope.register.UserID =$scope.smartcard.UserID;
            console.log($scope.register);
            $scope.PermissionPass = true;
            if($scope.smartcard.Evaluate == 'Y'){
                $scope.Evaluated = true;
            }
        }
    }else{
        setTimeout(function(){
            window.location.href="#/home";
        },20000);
    }

});

app.controller('RegisterSuccessController',function($scope, $routeParams){
    console.log('success page');
    $scope.gotoRegister = function(){
        // window.location.replace('#/register');  
        window.location.replace('#/');  
    }
});

app.controller('EvaluateController',function($scope, $routeParams, $filter, HTTPService, IndexOverlayFactory){

    IndexOverlayFactory.overlayShow();
    // $scope.$parent.menu_selected = 'evaluate';
    var $user_session = sessionStorage.getItem('user_authen_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);        
    }else{
       window.location.replace('#/register');
    }

    IndexOverlayFactory.overlayHide();

    $scope.getQuestions = function(){
        HTTPService.clientRequest('getQuestions',{}).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.QuestionList = result.data.DATA.Questions;
                $scope.QuestionTypeList = result.data.DATA.QuestionSection;
                console.log($scope.QuestionList);
                $scope.QuestionList.splice(0,2);
                $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
                console.log($scope.QuestionList);
                $scope.BG_IMG = $scope.QuestionList[0].background_img;
                $scope.setEmptyUnselectQuestion();
            }else{
                alert('ไม่สามารถโหลดแบบสอบถามได้ กรุณากดปุ่ม F5 เพื่อโหลดแบบสอบถามอีกครั้ง');
            }
        });
    }

    $scope.checkValue = function(val, data){
        // console.log(data);
        if(val != ''){
            var index = $filter('FindQuestion')($scope.DoQuestions, data.QuestionID); 
            if(index == -1){ 
                $scope.DoQuestions.push(data);
                if($scope.TotalDoQuestion[$scope.ShowSection - 1] < $scope.TotalQuestion){
                    $scope.TotalDoQuestion[$scope.ShowSection - 1] += 1;
                }
            }else{
                $scope.DoQuestions[index] = data;
            }

            
        }else{
            if($scope.TotalDoQuestion[$scope.ShowSection - 1] > 0){
                $scope.TotalDoQuestion[$scope.ShowSection - 1] -= 1;
            }
        }
        // console.log($scope.DoQuestions);

        //Check all answer
        console.log($scope.TotalQuestion , $scope.TotalDoQuestion[$scope.ShowSection - 1]);
        if($scope.TotalQuestion == $scope.TotalDoQuestion[$scope.ShowSection - 1]){
            $scope.AllowNextButton = true;
        }else{
            $scope.AllowNextButton = false;
        }

        $scope.TotalPercent = Math.ceil(($scope.DoQuestions.length / $scope.QuestionList.length) * 100);
        console.log($scope.TotalPercent);
    }

    $scope.checkManyValue = function(val, data){

        var index = $filter('FindQuestion')($scope.QuestionList, data.QuestionID); 
        // console.log(index, $scope.QuestionList[index].choice_list);
        var reschk = [];
        angular.forEach($scope.QuestionList[index].choice_list, function(choice) {
            if (choice.answer !== undefined && choice.answer !== '') {
            reschk.push(choice.answer);
          }
        });
        // console.log(reschk);
        var chkStr = reschk.join('::');

        var answerdata = {};
        angular.copy(data, answerdata);
        answerdata.answer = chkStr;
        var answer_index = $filter('FindQuestion')($scope.DoQuestions, answerdata.QuestionID); 
        if(answer_index == -1){ 
            $scope.DoQuestions.push(answerdata);

            if($scope.TotalDoQuestion[$scope.ShowSection - 1] < $scope.TotalQuestion){
                $scope.TotalDoQuestion[$scope.ShowSection - 1] += 1;
            }
        }else{
            $scope.DoQuestions[answer_index] = answerdata;
        }

        if(chkStr == ''){
            $scope.TotalDoQuestion[$scope.ShowSection - 1] -= 1;
            $scope.DoQuestions.splice(answer_index, 1);
        }
        // console.log($scope.DoQuestions);

        //Check all answer
        if($scope.TotalQuestion == $scope.TotalDoQuestion[$scope.ShowSection - 1]){
            $scope.AllowNextButton = true;
        }else{
            $scope.AllowNextButton = false;
        }
        $scope.TotalPercent = Math.ceil(($scope.DoQuestions.length / $scope.QuestionList.length) * 100);
        // console.log($scope.DoQuestions);

        // add / remove question if parent question and choice != null
        var i = 0;
        while(i < $scope.UnselectQuestion.length){
            
            if( answerdata.answer.indexOf($scope.UnselectQuestion[i].QuestionData.ParentChoice) > -1 ){
                var questionData = angular.copy($scope.UnselectQuestion[i]);
                console.log(answerdata.answer, (questionData.QuestionData.ParentChoice));
                $scope.QuestionList.splice(questionData.index, 0, questionData.QuestionData);
                // setTimeout(function(){
                $scope.UnselectQuestion.splice(i, 1);    
                // }, 1000);
                
            }else{
                var j = 0;
                while(j < $scope.QuestionList.length){
                    if(($scope.QuestionList[j].ParentQuestion != null && $scope.QuestionList[j].ParentQuestion != '') 
                        && ($scope.QuestionList[j].ParentChoice != null && $scope.QuestionList[j].ParentChoice != '')
                        && answerdata.answer.indexOf($scope.QuestionList[j].ParentChoice) == -1){

                        var Data = angular.copy($scope.QuestionList[j]);
                        $scope.QuestionList.splice(j, 1);

                        $scope.UnselectQuestion.push({'QuestionData' : Data, 'index' : j});

                    }else{
                        j++;
                    }
                }
            }
            i++;
        }
        $scope.QuestionList.sort(compare);
        // $scope.setEmptyUnselectQuestion();
        console.log($scope.QuestionList);
    }

    function compare(a,b) {
      if (a.QuestionsSection < b.QuestionsSection)
        return -1;
      if (a.QuestionsSection > b.QuestionsSection)
        return 1;
      return 0;
    }



    $scope.backStep = function(){
        $scope.ShowSection -= 1;
        $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
        // console.log('show sectoin : ' + $scope.ShowSection);
        if($scope.TotalQuestion == $scope.TotalDoQuestion[$scope.ShowSection - 1]){
            $scope.AllowNextButton = true;
        }else{
            $scope.AllowNextButton = false;
        }
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    $scope.nextStep = function(){
        $scope.ShowSection += 1;
        $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
        // console.log('show sectoin : ' + $scope.ShowSection);
        if($scope.TotalQuestion == $scope.TotalDoQuestion[$scope.ShowSection - 1]){
            $scope.AllowNextButton = true;
        }else{
            $scope.AllowNextButton = false;
        }
        document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    console.log($scope.currentUser);
    $scope.finish = function(){

        var params = {'DoQuestions':$scope.DoQuestions
                    , 'Suggestion':$scope.Suggestion.data
                    , 'UserID':$scope.currentUser.UserID};
        HTTPService.clientRequest('sendEvaluate',params).then(function(result){
            if(result.data.STATUS == 'OK'){
                window.location.replace('#/evaluate-success');
                // $scope.QuestionList = result.data.DATA;
                // $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
            }else{
                alert('ไม่สามารถบันทึกแบบสอบถามได้ กรุณากดส่งแบบสอบถามอีกครั้ง');
            }
        });
    }

    $scope.prevQuestion = function(){
        $scope.QuestionNo--;
        // $scope.ShowSection = $scope.QuestionList[$scope.QuestionNo - 1].QuestionsSection;
        $scope.findSectionIndex($scope.QuestionNo - 1);
        $scope.BG_IMG = $scope.QuestionList[$scope.QuestionNo - 1].background_img;
        // console.log($scope.QuestionNo);
    }
    $scope.nextQuestion = function(){
        $scope.QuestionNo++;
        // $scope.ShowSection = $scope.QuestionList[$scope.QuestionNo - 1].QuestionsSection;
        $scope.findSectionIndex($scope.QuestionNo - 1);
        $scope.BG_IMG = $scope.QuestionList[$scope.QuestionNo - 1].background_img;
        // console.log($scope.QuestionNo);
    }

    $scope.findSectionIndex = function(questionNo){
        for(var i = 0; i < $scope.QuestionTypeList.length; i++){
            // console.log($scope.QuestionTypeList[i].id.toString() , $scope.QuestionList[questionNo].QuestionsSection);
            if($scope.QuestionTypeList[i].id.toString() == $scope.QuestionList[questionNo].QuestionsSection){
                $scope.ShowSection = i;
                break;
            }
        }
    }

    $scope.setEmptyUnselectQuestion = function(){
        for(var i = 0; i < $scope.QuestionList.length; i++){
            if(($scope.QuestionList[i].ParentQuestion != null && $scope.QuestionList[i].ParentQuestion != '') && ($scope.QuestionList[i].ParentChoice != null && $scope.QuestionList[i].ParentChoice != '')){
                $scope.UnselectQuestion.push({'QuestionData' : $scope.QuestionList[i], 'index' : i});
            }
        }
        var i = 0;
        while(i < $scope.QuestionList.length){
            if(($scope.QuestionList[i].ParentQuestion != null && $scope.QuestionList[i].ParentQuestion != '') && ($scope.QuestionList[i].ParentChoice != null && $scope.QuestionList[i].ParentChoice != '')){
                $scope.QuestionList.splice(i, 1);
            }else{
                i++;
            }
        }
        console.log($scope.UnselectQuestion);
        console.log($scope.QuestionList);
    }

    $scope.UnselectQuestion = [];
    $scope.BG_IMG = '';
    $scope.TotalPercent = 0;
    $scope.ShowSection = 0;
    $scope.QuestionTypeList =[{'section':'ส่วนที่ 1 ข้อมูลทั่วไปของผู้ตอบแบบสอบถาม','sub_section':''}
                            ,{'section':'ส่วนที่ 2  ความคิดเห็นเกี่ยวกับการแสดงความพึงพอใจในการจัดงานเทศกาลโคนมแห่งชาติ ประจำปี 2561','section':'ความพึงพอใจด้านกิจกรรมต่างๆภายในงาน'}
                            ,{'section':'ส่วนที่ 2  ความคิดเห็นเกี่ยวกับการแสดงความพึงพอใจในการจัดงานเทศกาลโคนมแห่งชาติ ประจำปี 2561','section':'ความพึงพอใจด้านสถานที่'}
                            ,{'section':'ส่วนที่ 2  ความคิดเห็นเกี่ยวกับการแสดงความพึงพอใจในการจัดงานเทศกาลโคนมแห่งชาติ ประจำปี 2561','section':'ความพึงพอใจด้านการบริการและอื่นๆ'}
                            ,{'section':'ส่วนที่ 2  ความคิดเห็นเกี่ยวกับการแสดงความพึงพอใจในการจัดงานเทศกาลโคนมแห่งชาติ ประจำปี 2561','section':'ภาพรวมของการจัดงาน'}];
    $scope.getQuestions();
    $scope.DoQuestions = [];
    $scope.Suggestion = {'data' : ''};
    $scope.TotalDoQuestion = [0,0,0,0,0];

    $scope.AllowNextButton = false;

    $scope.QuestionNo = 1;
});

app.controller('EvaluateSuccessController',function($scope, $routeParams){
    console.log('success page');
    sessionStorage.removeItem('user_session');
    sessionStorage.removeItem('user_authen_session');
    $scope.closeTab = function(){
        window.top.close();
        window.location.replace('#/home');
    }

    setTimeout(function(){
        $scope.closeTab();
    },5000);
});

app.controller('ImageController',function($scope, $routeParams){
    console.log('success page');
    $scope.imgname = $routeParams.imgname;
});

app.controller('SearchIDCardController',function($scope, $routeParams, HTTPService){
    console.log('success page');
    $scope.imgname = $routeParams.imgname;

    $scope.checkIDCard = function(idCard){
        HTTPService.clientRequest('findRegisteredWithIDCard', {'IDCard' : idCard}).then(function(result){

            if(result.data.STATUS == 'OK'){
                window.location.replace('#/smartcard-register/' + result.data.DATA.UserDataEncode + '/nonidcard');
                // $scope.QuestionList = result.data.DATA;
                // $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
            }else{
                //alert(result.data.DATA);
                window.location.href = '#/registration/' + idCard;
            }
        });
    }
});

app.controller('RegisterForAdminController',function($scope, $routeParams, IndexOverlayFactory, HTTPService){
    
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = 'register-for-admin';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        // console.log($scope.$parent.currentUser);
        // console.log($scope.$parent.currentUser.Firstname);
        
    }else{
       window.location.replace('#/logon');
    }

    IndexOverlayFactory.overlayHide();
    if($scope.currentUser.AdminType != 'SUPERS' && $scope.currentUser.AdminType != 'NORMAL'){
        alert('คุณไม่มีสิทธิ์ใช้งานหน้านี้');
        window.location.replace('#/logon');
        return false;
    }

    $scope.saveFrontRegister = function(obj, saveType){
        HTTPService.clientRequest('saveFrontRegister', obj).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.Attendee = result.data.DATA;
                console.log($scope.Attendee);
                if(saveType == 'DRAFT'){
                    window.location.href="#/attendee-list";

                }else if(saveType == 'GOOLDER'){
                    // sessionStorage.setItem('user_authen_session' , JSON.stringify($scope.Attendee));
                    window.location.href = '#/evaluate-admin/' + $scope.Attendee.UserID;
                }else if(saveType == 'GOYOUNGER'){
                    // sessionStorage.setItem('user_authen_session' , JSON.stringify($scope.Attendee));
                    window.location.href = '#/evaluate-admin/' + $scope.Attendee.UserID + '/ALL';
                }
            }else{
                alert(result.data.DATA);   
            }
        });
    }

    $scope.Attendee = null;
    $scope.regisform = {'Firstname':null, 'Lastname':null};
    // console.log($scope.regisform);

});

app.controller('EvaluateAdminController',function($scope, $routeParams, $filter, HTTPService, IndexOverlayFactory){

    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = 'register-for-admin';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        // console.log($scope.$parent.currentUser);
        // console.log($scope.$parent.currentUser.Firstname);
        
    }else{
       window.location.replace('#/logon');
    }

    IndexOverlayFactory.overlayHide();
    if($scope.currentUser.AdminType != 'SUPERS' && $scope.currentUser.AdminType != 'NORMAL'){
        alert('คุณไม่มีสิทธิ์ใช้งานหน้านี้');
        window.location.replace('#/logon');
        return false;
    }


    $scope.getQuestions = function(){
        HTTPService.clientRequest('getQuestions',{}).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.QuestionList = result.data.DATA;
                $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
                if($routeParams.DisplayType != undefined){
                    $scope.DisplayType = $routeParams.DisplayType;
                    // Auto set values
                    var loop = $scope.QuestionList.length;
                    var doIndex = 0;
                    for(var i = 0; i < loop; i++){
                        if($scope.QuestionList[i].DisplayType == 'OLD' && $scope.QuestionList[i].Questions != 'ช่วงอายุ'){
                            var data = $scope.QuestionList[i].choice_list[ $scope.QuestionList[i].choice_list.length - 1 ];   //$scope.QuestionList[i].choice_list.length - 1
                            data.answer = data.ChoiceDesc;
                            $scope.DoQuestions[doIndex] = data;
                            doIndex++;
                        }
                    }
                    $scope.QuestionList[1].choice_list[0].answer = $scope.QuestionList[1].choice_list[0].ChoiceDesc;
                    $scope.DoQuestions[doIndex] = $scope.QuestionList[1].choice_list[0];
                    console.log($scope.DoQuestions);
                }
                
            }else{
                alert('ไม่สามารถโหลดแบบสอบถามได้ กรุณากดปุ่ม F5 เพื่อโหลดแบบสอบถามอีกครั้ง');
            }
        });
    }

    $scope.filterEvaluate = function(question){
        if($scope.DisplayType == 'ALL'){
            return (question.QuestionsSection == $scope.ShowSection 
                    && question.DisplayType == $scope.DisplayType );
        }else{
            console.log(question , $scope.ShowSection);
            return (question.QuestionsSection == $scope.ShowSection );
        }
    }

    $scope.checkValue = function(val, data){
        // console.log(data);
        if(val != ''){
            var index = $filter('FindQuestion')($scope.DoQuestions, data.QuestionID); 
            if(index == -1){ 
                $scope.DoQuestions.push(data);
                if($scope.TotalDoQuestion[$scope.ShowSection - 1] < $scope.TotalQuestion){
                    $scope.TotalDoQuestion[$scope.ShowSection - 1] += 1;
                }
            }else{
                $scope.DoQuestions[index] = data;
            }

            
        }else{
            if($scope.TotalDoQuestion[$scope.ShowSection - 1] > 0){
                $scope.TotalDoQuestion[$scope.ShowSection - 1] -= 1;
            }
        }
        // console.log($scope.DoQuestions);

        //Check all answer
        console.log($scope.TotalQuestion , $scope.TotalDoQuestion[$scope.ShowSection - 1]);
        if($scope.TotalQuestion == $scope.TotalDoQuestion[$scope.ShowSection - 1]){
            $scope.AllowNextButton = true;
        }else{
            $scope.AllowNextButton = false;
        }
    }

    $scope.checkManyValue = function(val, data){

        var index = $filter('FindQuestion')($scope.QuestionList, data.QuestionID); 
        // console.log(index, $scope.QuestionList[index].choice_list);
        var reschk = [];
        angular.forEach($scope.QuestionList[index].choice_list, function(choice) {
            if (choice.answer !== undefined && choice.answer !== '') {
            reschk.push(choice.answer);
          }
        });
        // console.log(reschk);
        var chkStr = reschk.join('::');

        var answerdata = {};
        angular.copy(data, answerdata);
        answerdata.answer = chkStr;
        var answer_index = $filter('FindQuestion')($scope.DoQuestions, answerdata.QuestionID); 
        if(answer_index == -1){ 
            $scope.DoQuestions.push(answerdata);

            if($scope.TotalDoQuestion[$scope.ShowSection - 1] < $scope.TotalQuestion){
                $scope.TotalDoQuestion[$scope.ShowSection - 1] += 1;
            }
        }else{
            $scope.DoQuestions[answer_index] = answerdata;
        }

        if(chkStr == ''){
            $scope.TotalDoQuestion[$scope.ShowSection - 1] -= 1;
            $scope.DoQuestions.splice(answer_index, 1);
        }
        // console.log($scope.DoQuestions);

        //Check all answer
        if($scope.TotalQuestion == $scope.TotalDoQuestion[$scope.ShowSection - 1]){
            $scope.AllowNextButton = true;
        }else{
            $scope.AllowNextButton = false;
        }
    }

    $scope.backStep = function(){
        $scope.ShowSection -= 1;
        $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
        console.log('show sectoin : ' + $scope.ShowSection);
        if($scope.TotalQuestion == $scope.TotalDoQuestion[$scope.ShowSection - 1]){
            $scope.AllowNextButton = true;
        }else{
            $scope.AllowNextButton = false;
        }
        document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    $scope.nextStep = function(){
        $scope.ShowSection += 1;
        $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
        console.log('show sectoin : ' + $scope.ShowSection);
        if($scope.TotalQuestion == $scope.TotalDoQuestion[$scope.ShowSection - 1]){
            $scope.AllowNextButton = true;
        }else{
            $scope.AllowNextButton = false;
        }
        document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    $scope.finish = function(){

        var params = {'DoQuestions':$scope.DoQuestions,'UserID':$scope.UserID, 'ResponseType':'MANUAL'};
        HTTPService.clientRequest('sendEvaluate',params).then(function(result){
            if(result.data.STATUS == 'OK'){
                alert('บันทึกแบบสอบถามสำเร็จ');
                window.location.replace('#/attendee-list');
                // $scope.QuestionList = result.data.DATA;
                // $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
            }else{
                alert('ไม่สามารถบันทึกแบบสอบถามได้ กรุณากดส่งแบบสอบถามอีกครั้ง');
            }
        });
    }

    $scope.ShowSection = 1;
    $scope.QuestionTypeList =[{'section':'ส่วนที่ 1 ข้อมูลทั่วไปของผู้ตอบแบบสอบถาม','sub_section':''}
                            ,{'section':'ส่วนที่ 2  ความคิดเห็นเกี่ยวกับการแสดงความพึงพอใจในการจัดงานเทศกาลโคนมแห่งชาติ ประจำปี 2561','section':'ความพึงพอใจด้านกิจกรรมต่างๆภายในงาน'}
                            ,{'section':'ส่วนที่ 2  ความคิดเห็นเกี่ยวกับการแสดงความพึงพอใจในการจัดงานเทศกาลโคนมแห่งชาติ ประจำปี 2561','section':'ความพึงพอใจด้านสถานที่'}
                            ,{'section':'ส่วนที่ 2  ความคิดเห็นเกี่ยวกับการแสดงความพึงพอใจในการจัดงานเทศกาลโคนมแห่งชาติ ประจำปี 2561','section':'ความพึงพอใจด้านการบริการและอื่นๆ'}
                            ,{'section':'ส่วนที่ 2  ความคิดเห็นเกี่ยวกับการแสดงความพึงพอใจในการจัดงานเทศกาลโคนมแห่งชาติ ประจำปี 2561','section':'ภาพรวมของการจัดงาน'}];
    $scope.getQuestions();
    $scope.DoQuestions = [];
    
    $scope.TotalDoQuestion = [0,0,0,0,0];

    $scope.AllowNextButton = false;

    $scope.UserID = $routeParams.UserID;
    $scope.DisplayType = '';
    
});

app.controller('ManageAdminController',function($scope, $routeParams, HTTPService, IndexOverlayFactory){
    $scope.$parent.menu_selected = 'manage-admin';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        // console.log($scope.$parent.currentUser);
        // console.log($scope.$parent.currentUser.Firstname);
        
    }else{
       window.location.replace('#/logon');
    }

    IndexOverlayFactory.overlayHide();
    if($scope.currentUser.AdminType != 'SUPERS' && $scope.currentUser.AdminType != 'NORMAL'){
        alert('คุณไม่มีสิทธิ์ใช้งานหน้านี้');
        window.location.replace('#/logon');
        return false;
    }

    $scope.saveAdmin = function(admin){
        HTTPService.clientRequest('saveAdmin', admin).then(function(result){
            if(result.data.STATUS == 'OK'){
                alert('เพิ่มผู้ดูแลระบบสำเร็จ');
                window.location.replace('#/attendee-list');
                // $scope.QuestionList = result.data.DATA;
                // $scope.TotalQuestion = $filter('CountAnswers')($scope.QuestionList, $scope.ShowSection);
            }else{
                alert('ไม่สามารถเพิ่มผู้ดูแลระบบได้ ' + result.data.DATA);
            }
        });
    }

});

app.controller('ReportController',function($scope, $routeParams, HTTPService, IndexOverlayFactory){
    $scope.$parent.menu_selected = 'report';
    IndexOverlayFactory.overlayShow();
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        // console.log($scope.$parent.currentUser);
        // console.log($scope.$parent.currentUser.Firstname);
        
    }else{
       window.location.replace('#/logon');
    }

    IndexOverlayFactory.overlayHide();
    if($scope.currentUser.AdminType != 'SUPERS' && $scope.currentUser.AdminType != 'NORMAL'){
        alert('คุณไม่มีสิทธิ์ใช้งานหน้านี้');
        window.location.replace('#/logon');
        return false;
    }

    $scope.exportExcel = function(condition){
        IndexOverlayFactory.overlayShow();
        if((condition.years - 543) != curYear){
            // alert(curYear);
            window.location.href="downloads/archeive_file/dairyfair_report_" + condition.years + '.xlsx';
        }else{
            
            HTTPService.clientRequest('exportExcel', {'condition' : condition}).then(function(result){
                if(result.data.STATUS == 'OK'){
                    window.location.href="downloads/" + result.data.DATA;
                }else{
                    alert('ไม่สามารถออกรายงานได้');
                }
                IndexOverlayFactory.overlayHide();
            });
        }
    }

    $scope.loadSummary = function(condition){
        IndexOverlayFactory.overlayShow();
        HTTPService.clientRequest('loadSummary', {'condition' : condition}).then(function(result){
            if(result.data.STATUS == 'OK'){
                $scope.CountRegister = result.data.DATA.CountRegister;
                $scope.CountEvaluate = result.data.DATA.CountEvaluate;
            }else{
                alert('ไม่สามารถออกรายงานได้');
            }
            IndexOverlayFactory.overlayHide();
        });
    }

    var curDate = new Date();
    var curYear = curDate.getFullYear();
    var startYear = 2018;
    $scope.YearList = getYearListFromBegin(startYear, curYear);
    $scope.condition = {'years' : (curYear + 543)};

    $scope.loadSummary($scope.condition);

});