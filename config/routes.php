<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {

    $routes->add('test', '/api/user')
        ->controller('App\Controller\Api\UserController::index')
        ->methods(['GET']);

    $routes->add('user_register', '/api/register')
        ->controller('App\Controller\Api\UserController::register')
        ->methods(['POST']);

    $routes->add('user_login', '/api/login')
        ->controller('App\Controller\Api\UserController::login')
        ->methods(['POST']);
    
    $routes->add('send_invitation', '/api/invite')
        ->controller('App\Controller\InviteController::sendInvitation')
        ->methods(['POST']);
};


