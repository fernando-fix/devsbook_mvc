<?php
use core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/login', 'LoginController@signin'); //logar
$router->post('/login', 'LoginController@signinAction'); //logar action
$router->get('/cadastro', 'LoginController@signup'); //cadastrar
$router->post('/cadastro', 'LoginController@signupAction'); //cadastrar action
