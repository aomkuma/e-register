<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    $logger->pushHandler(new Monolog\Handler\RotatingFileHandler($settings['path'], $settings['maxFiles'], $settings['level']));
    return $logger;
};

$container['db'] = function ($c) {
    $settings = $c->get('settings')['db'];
    $capsule = new Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($settings);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    return $capsule;
};

$container['sms'] = function ($c) {
    $settings = $c->get('settings')['sms'];
    return $settings;
};

$container['ldap'] = function ($c) {
    $settings = $c->get('settings')['ldap'];
    return $settings;
};

$container['UserController'] = function ($c) {
    return new \App\Controller\UserController($c->get('logger'), $c->get('db'));
};

$container['LoginController'] = function ($c) {
    return new \App\Controller\LoginController($c->get('logger'), $c->get('sms'), $c->get('db'),  $c->get('ldap'));
};

$container['RegionController'] = function ($c) {
    return new \App\Controller\RegionController($c->get('logger'), $c->get('db'));
};

$container['AttendeeController'] = function ($c) {
    return new \App\Controller\AttendeeController($c->get('logger'), $c->get('db'));
};

$container['EvaluateController'] = function ($c) {
    return new \App\Controller\EvaluateController($c->get('logger'), $c->get('db'), $c->get('sms'));
};

$container['Mailer'] = function ($c) {
    return new \App\Controller\Mailer($c->get('logger'));
};

$container['SMSController'] = function ($c) {
    return new \App\Controller\SMSController($c->get('logger'), $c->get('sms'));
};

$container['ReportController'] = function ($c) {
    return new \App\Controller\ReportController($c->get('logger'), $c->get('db'));
};
