app.controller('IconListController', function($scope, $uibModal, $location, $sce, HTTPService, IndexOverlayFactory) {
    //sessionStorage.removeItem('user_session');
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = 'manage-icon';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        
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
        
        // var params = {'keyword':condition.keyword, 'registerType':condition.registerType, 'offset':$scope.dataOffset};
        HTTPService.clientRequest('question/icon/list', null).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.DataList =  result.data.DATA.List;
                
            }
        });
    
    }

    $scope.saveData = function(Data){
        $scope.alertMessage = 'ต้องการบันทึกข้อมูลนี้ ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation : false,
            templateUrl : 'html/dialog_confirm.html',
            size : 'sm',
            scope : $scope,
            backdrop : 'static',
            controller : 'ModalDialogCtrl',
            resolve : {
                params : function() {
                    return {};
                } 
            },
        });

        modalInstance.result.then(function (valResult) {

            var params = {'Data' : Data};
            HTTPService.uploadRequest('question/icon/update', params).then(function(result){
                console.log(result);
                if(result.data.STATUS == 'OK'){
                    alert('บันทึกสำเร็จ');
                    $scope.PAGE = 'MAIN';
                    $scope.loadData($scope.condition);
                    // window.location.href = '#/question-detail/' + result.data.DATA.id;
                }else{
                    alert(result.data.DATA);
                }
                IndexOverlayFactory.overlayHide();
            });
        });
    }

    $scope.create = function(){
        $scope.Data = {'icon_name' : null, 'choice_icon_detail' : []};
        $scope.PAGE = 'UPDATE';
    }

    $scope.update = function(data){
        $scope.Data = angular.copy(data);
        $scope.PAGE = 'UPDATE';
    }

    $scope.cancelUpdate = function(page){
        $scope.PAGE = page;
    }

    $scope.addIcon = function(){
        $scope.Data.choice_icon_detail.push({
                            'id':''
                            ,'img_path':''
                            ,'points':null
                            ,'icon_description':''
                            ,'order_no':($scope.Data.choice_icon_detail.length + 1)
                        });
        $scope.PAGE = 'UPDATE';
    }

    $scope.removeIcon = function(id, index){
        if(id == ''){
            $scope.Data.choice_icon_detail.splice(index, 1);
        }else{
            $scope.alertMessage = 'รูปภาพจะถูกลบจากฐานข้อมูลทันที ต้องการลบข้อมูลนี้ ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : false,
                templateUrl : 'html/dialog_confirm.html',
                size : 'sm',
                scope : $scope,
                backdrop : 'static',
                controller : 'ModalDialogCtrl',
                resolve : {
                    params : function() {
                        return {};
                    } 
                },
            });

            modalInstance.result.then(function (valResult) {

                var params = {'id' : id};
                HTTPService.uploadRequest('question/icon/delete', params).then(function(result){
                    console.log(result);
                    if(result.data.STATUS == 'OK'){
                        $scope.Data.choice_icon_detail.splice(index, 1);
                        // window.location.href = '#/question-detail/' + result.data.DATA.id;
                    }else{
                        alert(result.data.DATA);
                    }
                    IndexOverlayFactory.overlayHide();
                });
            });
        }
    }

    $scope.PAGE = 'MAIN';
    $scope.loadData($scope.condition);

});


app.controller('QuestionListController', function($scope, $uibModal, $location, $sce, HTTPService, IndexOverlayFactory) {
    //sessionStorage.removeItem('user_session');
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = 'manage-question';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        
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
        
        // var params = {'keyword':condition.keyword, 'registerType':condition.registerType, 'offset':$scope.dataOffset};
        HTTPService.clientRequest('question/year/list', null).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.tableLoad = false;
                $scope.DataList =  result.data.DATA.List;
                
            }
        });
    
    }

    $scope.saveData = function(Data){
        $scope.alertMessage = 'ต้องการบันทึกข้อมูลนี้ ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation : false,
            templateUrl : 'html/dialog_confirm.html',
            size : 'sm',
            scope : $scope,
            backdrop : 'static',
            controller : 'ModalDialogCtrl',
            resolve : {
                params : function() {
                    return {};
                } 
            },
        });

        modalInstance.result.then(function (valResult) {

            var params = {'Data' : Data};
            HTTPService.uploadRequest('question/year/update', params).then(function(result){
                console.log(result);
                if(result.data.STATUS == 'OK'){
                    alert('บันทึกสำเร็จ');
                    // $scope.PAGE = 'MAIN';
                    window.location.href = '#/question-detail/' + result.data.DATA.id;
                }else{
                    alert(result.data.DATA);
                }
                IndexOverlayFactory.overlayHide();
            });
        });
    }

    $scope.createQuestionYear = function(){
        $scope.Data = {'years' : null, 'actives' : 'Y'};
        $scope.ShowALert = '';
        $scope.PAGE = 'UPDATE';
    }

    $scope.checkDuplicateData = function(year){
        $scope.ShowALert = '';
        for(var i = 0; i < $scope.DataList.length; i++){
            if(year == $scope.DataList[i].years){
                $scope.ShowALert = 'มีในฐานข้อมูลแล้ว ไม่สามารถสร้างซ้ำได้';
            }
        }
    }

    $scope.cancelUpdate = function(page){
        $scope.PAGE = page;
    }

    $scope.YearList = getYearList(10);

    $scope.PAGE = 'MAIN';
    $scope.loadData($scope.condition);

});

app.controller('QuestionDetailController', function($scope, $uibModal, $location, $sce, $routeParams, HTTPService, IndexOverlayFactory) {
    //sessionStorage.removeItem('user_session');
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = 'manage-question';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        
    }else{
       window.location.replace('#/logon');
    }

    $scope.question_year_id = $routeParams.question_year_id;

    if($scope.currentUser.AdminType != 'SUPERS' && $scope.currentUser.AdminType != 'NORMAL'){
        alert('คุณไม่มีสิทธิ์ใช้งานหน้านี้');
        window.location.replace('#/logon');
        return false;
    }

    IndexOverlayFactory.overlayHide();

    $scope.loadDataYear = function(){
        
        var params = {'question_year_id':$scope.question_year_id};
        HTTPService.clientRequest('question/year/get', params).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.QuestionYear =  result.data.DATA;
            }
        });
    
    }

    $scope.loadData = function(condition){
        
        var params = {'question_year_id':$scope.question_year_id};
        HTTPService.clientRequest('question/detail/list', params).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.tableLoad = false;
                $scope.DataList = result.data.DATA.List;
                
            }
        });
    
    }

    $scope.saveData = function(Data){
        $scope.alertMessage = 'ต้องการบันทึกข้อมูลนี้ ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation : false,
            templateUrl : 'html/dialog_confirm.html',
            size : 'sm',
            scope : $scope,
            backdrop : 'static',
            controller : 'ModalDialogCtrl',
            resolve : {
                params : function() {
                    return {};
                } 
            },
        });

        modalInstance.result.then(function (valResult) {

            var params = {'Data' : Data};
            HTTPService.uploadRequest('question/section/update', params).then(function(result){
                console.log(result);
                if(result.data.STATUS == 'OK'){
                    alert('บันทึกสำเร็จ');
                    $scope.Data = null;
                    $scope.loadData($scope.condition);
                    // $scope.PAGE = 'MAIN';
                    // window.location.href = '$/question-detail/' + result.data.DATA.id;
                }else{
                    alert(result.data.DATA);
                }
                IndexOverlayFactory.overlayHide();
            });
        });
    }

    $scope.createSection = function(){
        $scope.Data = {'question_year_id' : $scope.question_year_id, 'order_no' : $scope.DataList.length + 1};
        // $scope.ShowALert = '';
        // $scope.PAGE = 'UPDATE-SECTION';
    }

    $scope.updateSection = function(data){
        $scope.Data = angular.copy(data);
        // $scope.ShowALert = '';
        // $scope.PAGE = 'UPDATE-SECTION';
    }

    $scope.cancelUpdate = function(page){
        $scope.PAGE = page;
    }

    $scope.PAGE = 'MAIN';
    $scope.loadDataYear();
    $scope.loadData($scope.condition);

});

app.controller('QuestionDetailController', function($scope, $uibModal, $location, $sce, $routeParams, HTTPService, IndexOverlayFactory) {
    //sessionStorage.removeItem('user_session');
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = 'manage-question';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        
    }else{
       window.location.replace('#/logon');
    }

    $scope.question_year_id = $routeParams.question_year_id;

    if($scope.currentUser.AdminType != 'SUPERS' && $scope.currentUser.AdminType != 'NORMAL'){
        alert('คุณไม่มีสิทธิ์ใช้งานหน้านี้');
        window.location.replace('#/logon');
        return false;
    }

    IndexOverlayFactory.overlayHide();

    $scope.loadDataYear = function(){
        
        var params = {'question_year_id':$scope.question_year_id};
        HTTPService.clientRequest('question/year/get', params).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.QuestionYear =  result.data.DATA;
            }
        });
    
    }

    $scope.loadData = function(condition){
        
        var params = {'question_year_id':$scope.question_year_id};
        HTTPService.clientRequest('question/detail/list', params).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.tableLoad = false;
                $scope.DataList = result.data.DATA.List;
                
            }
        });
    
    }

    $scope.saveData = function(Data){
        $scope.alertMessage = 'ต้องการบันทึกข้อมูลนี้ ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation : false,
            templateUrl : 'html/dialog_confirm.html',
            size : 'sm',
            scope : $scope,
            backdrop : 'static',
            controller : 'ModalDialogCtrl',
            resolve : {
                params : function() {
                    return {};
                } 
            },
        });

        modalInstance.result.then(function (valResult) {

            var params = {'Data' : Data};
            HTTPService.uploadRequest('question/section/update', params).then(function(result){
                console.log(result);
                if(result.data.STATUS == 'OK'){
                    alert('บันทึกสำเร็จ');
                    $scope.Data = null;
                    $scope.loadData($scope.condition);
                    // $scope.PAGE = 'MAIN';
                    // window.location.href = '$/question-detail/' + result.data.DATA.id;
                }else{
                    alert(result.data.DATA);
                }
                IndexOverlayFactory.overlayHide();
            });
        });
    }

    $scope.createSection = function(){
        $scope.Data = {'question_year_id' : $scope.question_year_id, 'order_no' : $scope.DataList.length + 1};
        // $scope.ShowALert = '';
        // $scope.PAGE = 'UPDATE-SECTION';
    }

    $scope.updateSection = function(data){
        $scope.Data = angular.copy(data);
        // $scope.ShowALert = '';
        // $scope.PAGE = 'UPDATE-SECTION';
    }

    $scope.cancelUpdate = function(page){
        $scope.PAGE = page;
    }

    $scope.backPage = function(){
        history.back();
    }

    $scope.PAGE = 'MAIN';
    $scope.loadDataYear();
    $scope.loadData($scope.condition);

});

app.controller('QuestionDataController', function($scope, $uibModal, $location, $sce, $routeParams, HTTPService, IndexOverlayFactory) {
    //sessionStorage.removeItem('user_session');
    IndexOverlayFactory.overlayShow();
    $scope.$parent.menu_selected = 'manage-question';
    var $user_session = sessionStorage.getItem('user_session');
    
    if($user_session != null){
        $scope.$parent.currentUser = angular.fromJson($user_session);
        
    }else{
       window.location.replace('#/logon');
    }

    $scope.question_section_id = $routeParams.question_section_id;

    if($scope.currentUser.AdminType != 'SUPERS' && $scope.currentUser.AdminType != 'NORMAL'){
        alert('คุณไม่มีสิทธิ์ใช้งานหน้านี้');
        window.location.replace('#/logon');
        return false;
    }

    IndexOverlayFactory.overlayHide();

    $scope.loadDataSection = function(){
        
        var params = {'id':$scope.question_section_id};
        HTTPService.clientRequest('question/section/get', params).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.QuestionSection =  result.data.DATA;
                $scope.loadDataYear();
            }
        });
    
    }

    $scope.loadDataYear = function(){
        
        var params = {'question_year_id':$scope.QuestionSection.question_year_id};
        HTTPService.clientRequest('question/year/get', params).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.QuestionYear =  result.data.DATA;
                $scope.loadAllQuestion();
            }
        });
    
    }

    $scope.loadData = function(condition){
        
        var params = {'question_section_id':$scope.question_section_id};
        HTTPService.clientRequest('question/data/list', params).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.DataList = result.data.DATA.List;

                for(var i = 0; i < $scope.DataList.length; i++){
                    $scope.changeQuestionChoice($scope.DataList[i].ParentQuestion, i);
                }
            }
        });
    
    }

    $scope.loadAllQuestion = function(){
        
        var params = {'question_year_id':$scope.QuestionSection.question_year_id};
        HTTPService.clientRequest('question/data/list', params).then(function (result) {
            if(result.data.STATUS == 'OK'){
                $scope.QuestionDataList = result.data.DATA.List;
                $scope.loadData($scope.condition);
            }
        });
    }

    $scope.saveData = function(Data){
        $scope.alertMessage = 'ต้องการบันทึกข้อมูลนี้ ใช่หรือไม่ ?';
        var modalInstance = $uibModal.open({
            animation : false,
            templateUrl : 'html/dialog_confirm.html',
            size : 'sm',
            scope : $scope,
            backdrop : 'static',
            controller : 'ModalDialogCtrl',
            resolve : {
                params : function() {
                    return {};
                } 
            },
        });

        modalInstance.result.then(function (valResult) {

            var params = {'Data' : Data};
            HTTPService.uploadRequest('question/data/update', params).then(function(result){
                console.log(result);
                if(result.data.STATUS == 'OK'){
                    alert('บันทึกสำเร็จ');
                    $scope.Data = null;
                    // $scope.loadData($scope.condition);
                    window.location.reload();
                    // $scope.PAGE = 'MAIN';
                    // window.location.href = '$/question-detail/' + result.data.DATA.id;
                }else{
                    alert(result.data.DATA);
                }
                IndexOverlayFactory.overlayHide();
            });
        });
    }

    $scope.createSection = function(){
        $scope.Data = {'question_year_id' : $scope.question_year_id, 'order_no' : $scope.DataList.length + 1};
        // $scope.ShowALert = '';
        // $scope.PAGE = 'UPDATE-SECTION';
    }

    $scope.addQuestion = function(){
         
        $scope.DataList.push({
            'QuestionID':''
            ,'QuestionYearID':$scope.QuestionYear.id
            ,'Questions':''
            ,'QuestionsSection':$scope.QuestionSection.id
            ,'QuestionNo':($scope.DataList.length + 1)
            ,'QuestionType':'ONCE'
            ,'background_img':''
            ,'ParentQuestion':null
            ,'ParentChoice':null
            ,'actives':'Y'
            ,'choice_list':[]
            ,'ChoiceList':[]
        });
    }

    $scope.addChoice = function(question){
         console.log(question.choice_list);
        question.choice_list.push({
            'ChoiceID':''
            ,'choice_icon_id':''
            ,'ChoiceDesc':''
            ,'ChoicePoint':''
            ,'ImgPath':''
            ,'ChoiceOrder':(question.length + 1)
            ,'QuestionID':question.QuestionID
        });

        console.log(question.choice_list);
    }

    $scope.chooseIcon = function(question_index){
        console.log(question_index);
        $scope.ChoiceSelected = {'id' : ''};
        // var params = {'id' : Data.id, 'OrgType' : OrgType, 'ApproveStatus' : 'reject', 'ApproveComment' : valResult};
            HTTPService.clientRequest('question/icon/list', null).then(function(result){
                console.log(result);
                $scope.ChoiceIconList = result.data.DATA.List;

                if(result.data.STATUS == 'OK'){
                    
                    var modalInstance = $uibModal.open({
                        animation : false,
                        templateUrl : 'icon.html',
                        size : 'md',
                        scope : $scope,
                        backdrop : 'static',
                        controller : 'ModalDialogReturnFromOKBtnCtrl',
                        resolve : {
                            params : function() {
                                return {};
                            } 
                        },
                    });

                    modalInstance.result.then(function (valResult) {
                        console.log(valResult);
                        console.log($scope.ChoiceSelected);
                        
                        for(var i = 0; i < $scope.ChoiceIconList.length; i++ ){
                            if($scope.ChoiceIconList[i].id == $scope.ChoiceSelected.id){
                                
                                $scope.DataList[question_index].choice_list = [];
                                
                                for(var j = 0; j < $scope.ChoiceIconList[i].choice_icon_detail.length; j++){
                                    
                                    $scope.DataList[question_index].choice_list.push({
                                        'ChoiceID':''
                                            ,'choice_icon_id':$scope.ChoiceIconList[i].choice_icon_detail[j].id
                                            ,'ChoiceDesc':$scope.ChoiceIconList[i].choice_icon_detail[j].icon_description
                                            ,'ChoicePoint':$scope.ChoiceIconList[i].choice_icon_detail[j].points
                                            ,'ImgPath':$scope.ChoiceIconList[i].choice_icon_detail[j].img_path
                                            ,'ChoiceOrder':$scope.ChoiceIconList[i].choice_icon_detail[j].order_no
                                            // ,'QuestionID':$scope.Qoestion.QuestionID
                                    });

                                }
                                console.log($scope.DataList[question_index].choice_list);
                            }
                        }
                    });

                }else{
                    alert(result.data.DATA);
                }
                IndexOverlayFactory.overlayHide();
            });
        
    }

    $scope.updateSection = function(data){
        $scope.Data = angular.copy(data);
        // $scope.ShowALert = '';
        // $scope.PAGE = 'UPDATE-SECTION';
    }

    $scope.cancelUpdate = function(page){
        $scope.PAGE = page;
    }

    $scope.backPage = function(){
        history.back();
    }

    $scope.deleteQuestion = function(QuestionID, index){
        if(QuestionID == ''){
            $scope.DataList.splice(index, 1);
        }else{
            $scope.alertMessage = 'คำถามจะถูกลบจากฐานข้อมูลทันที ต้องการลบข้อมูลนี้ ใช่หรือไม่ ?';
            var modalInstance = $uibModal.open({
                animation : false,
                templateUrl : 'html/dialog_confirm.html',
                size : 'sm',
                scope : $scope,
                backdrop : 'static',
                controller : 'ModalDialogCtrl',
                resolve : {
                    params : function() {
                        return {};
                    } 
                },
            });

            modalInstance.result.then(function (valResult) {

                var params = {'QuestionID' : QuestionID};
                HTTPService.uploadRequest('question/data/delete', params).then(function(result){
                    console.log(result);
                    if(result.data.STATUS == 'OK'){
                        $scope.DataList.splice(index, 1);
                        // window.location.href = '#/question-detail/' + result.data.DATA.id;
                    }else{
                        alert(result.data.DATA);
                    }
                    IndexOverlayFactory.overlayHide();
                });
            });
        }
    }

    $scope.changeQuestionChoice = function(QuestionID, question_index){
        for(var i = 0; i < $scope.QuestionDataList.length; i++){
            if(QuestionID != null){
                
                if(QuestionID == $scope.QuestionDataList[i].QuestionID){
                    // $scope.DataList[question_index].forEach(function(element) { 
                        // element.ChoiceList = $scope.QuestionDataList[i].choice_list; 
                    // });
                    console.log(QuestionID , $scope.QuestionDataList[i].QuestionID);
                    $scope.DataList[question_index]['ChoiceList'] = $scope.QuestionDataList[i].choice_list;
                    console.log($scope.DataList[question_index]);
                }
            }
        }
    }

    $scope.PAGE = 'MAIN';
    $scope.loadDataSection();
    
    

});