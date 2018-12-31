function returnResponse(response){
   if(!tryParseJSON(response.data)){

      //the json is ok
        return response.data;
    }else{

      //the json is not ok
        console.log(response.data);
        return false;
    }
}

function returnErrorResponse(errResponse){
    var errorDesc = errResponse.status + ' : ' + errResponse.statusText ;
    
    if(errResponse.data.DATA != null){
        errorDesc += ':: Cause ' + errResponse.data.DATA.errorInfo[2];
        alert( errorDesc );
    }else{
        if(errResponse.status == 401){
            alert( 'ไม่มีสิทธิ์เข้าใช้งานในหน้านี้' );
            window.location.replace("#/");
        }else{
            alert( errorDesc );
        }
        
    }

    console.error('Error while fetching specific Item', errResponse);
    return errResponse;
}

function tryParseJSON (jsonString){
    try {
        var o = JSON.parse(jsonString);
        if (o && typeof o === "object") {
            return o;
        }
    }
    catch (e) { //alert(jsonString); 
    }

    return false;
};

app.factory('IndexOverlayFactory', function(){
	var indexVar = 
	{
		overlay : false,
		overlayHide : function() {this.overlay = false},
		overlayShow : function() {this.overlay = true}
	};	
	
	return indexVar;
});

app.factory('HTTPService', ['$http', '$q', function($http, $q){
    return {
        clientRequest : function(action, obj) {
            return $http.post(servicesUrl + '/dporegister/public/' + action + '/',{"obj":obj})
                .then(
                    function(response){
                        return returnResponse(response);                    
                    }, 
                    function(errResponse){
                        return returnErrorResponse(errResponse);
                    }
                );
        }
    }
}]);
