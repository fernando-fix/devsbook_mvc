<?php
use core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/login', 'LoginController@signin'); //logar
$router->post('/login', 'LoginController@signinAction'); //logar action
$router->get('/cadastro', 'LoginController@signup'); //cadastrar
$router->post('/cadastro', 'LoginController@signupAction'); //cadastrar action

$router->post('/post/new', 'PostController@new');

$router->get('/perfil/{id}', 'ProfileController@index');
$router->get('/perfil', 'ProfileController@index');

//$router->get('/pesquisar');
//$router->get('/amigos');
//$router->get('/fotos');
//$router->get('/config');

$router->get('/sair', 'HomeController@logout');
