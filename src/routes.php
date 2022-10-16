<?php
use core\Router;
use src\controllers\ProfileController;

$router = new Router();

$router->get('/', 'HomeController@index');
$router->get('/login', 'LoginController@signin'); //logar
$router->post('/login', 'LoginController@signinAction'); //logar action

$router->get('/cadastro', 'LoginController@signup'); //cadastrar
$router->post('/cadastro', 'LoginController@signupAction'); //cadastrar action

$router->post('/post/new', 'PostController@new');
$router->get('/post/{id}/delete', 'PostController@delete');

$router->get('/perfil/{id}/fotos', 'ProfileController@photos');
$router->get('/perfil/{id}/amigos', 'ProfileController@friends');
$router->get('/perfil/{id}/follow', 'ProfileController@follow');
$router->get('/perfil/{id}', 'ProfileController@index');
$router->get('/perfil', 'ProfileController@index');

$router->get('/amigos', 'ProfileController@friends');
$router->get('/fotos', 'ProfileController@photos');

$router->get('/pesquisa', 'SearchController@index');

$router->get('/sair', 'LoginController@logout');

$router->get('/config', 'ConfigController@index');
$router->post('/upconfig', 'ConfigController@updateAction');

$router->get('/ajax/like/{id}', 'AjaxController@like');
$router->post('/ajax/comment', 'AjaxController@comment');
$router->post('/ajax/upload', 'AjaxController@upload');