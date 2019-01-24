<?php
// Routes

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//$app->get('/user/{id}', 'UserController:getUser');
$app->post('/login/', 'LoginController:authenticate');
$app->post('/findWithIDCard/', 'LoginController:findWithIDCard');
$app->post('/findRegisteredWithIDCard/', 'LoginController:findRegisteredWithIDCard');

$app->post('/loginAdmin/', 'LoginController:authenticateAdmin');
$app->post('/register/', 'LoginController:register');
$app->post('/registration/', 'LoginController:registration');
$app->post('/saveFrontRegister/','LoginController:saveFrontRegister');
$app->post('/getProvince/', 'RegionController:getProvince');
$app->post('/attendeeList/', 'AttendeeController:getAttendeeList');
$app->post('/updateRewards/', 'AttendeeController:updateRewards');
$app->post('/updateRewardType/', 'AttendeeController:updateRewardType');
$app->post('/updateEvaluate/', 'AttendeeController:updateEvaluate');
$app->post('/getQuestions/', 'EvaluateController:getQuestions');
$app->post('/sendEvaluate/', 'EvaluateController:sendEvaluate');
$app->post('/saveAdmin/', 'LoginController:saveAdmin');
$app->post('/exportExcel/', 'ReportController:exportExcel');
$app->post('/loadSummary/', 'ReportController:loadSummary');
$app->post('/question/year/list/', 'QuestionController:getQuestionYearList');
$app->post('/question/year/update/', 'QuestionController:updateQuestionYear');
$app->post('/question/year/get/', 'QuestionController:getQuestionYear');
$app->post('/question/detail/list/', 'QuestionController:getQuestionDetailList');
$app->post('/question/section/update/', 'QuestionController:updateQuestionSection');
$app->post('/question/section/get/', 'QuestionController:getQuestionSection');
$app->post('/question/data/list/', 'QuestionController:getQuestionDataList');
$app->post('/question/data/update/', 'QuestionController:updateQuestionData');
$app->post('/question/icon/list/', 'QuestionController:getChoiceIconList');
$app->post('/question/icon/update/', 'QuestionController:updateIcon');
$app->post('/question/icon/delete/', 'QuestionController:deleteIconDetail');
$app->post('/question/data/delete/', 'QuestionController:deleteQuestionData');

// $app->post('/checkIDCardProfile/', 'LoginController:checkIDCardProfile');




// Default action
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
