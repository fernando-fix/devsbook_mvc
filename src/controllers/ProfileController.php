<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class ProfileController extends Controller {

    private $loggedUser;

    public function __construct() {
        $this->loggedUser = UserHandler::checklogin();
        if($this->loggedUser === false) {
            $this->redirect('/login');
        }
    }

    public function index($atts = []) {
        $page = intval(filter_input(INPUT_GET, 'page'));
        
        //detectanto o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        }

        //pegando informações do usuário
        $user = UserHandler::getUser($id, true);
        if($user == false) {
            $this->redirect('/');
        }

        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        //pegando o feed do usuário 
        $feed = PostHandler::getUserFeed(
            $id,
            $page,
            $this->loggedUser->id
        );

        //verificar se eu sigo o usuário
        $isFollowing = false;

        if($user->id != $this->loggedUser->id){
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);

        }

        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'feed' => $feed,
            'isFollowing' => $isFollowing
        ]);
    }

    public function follow($atts) {
        $to = intval($atts['id']);

        $exits = UserHandler::idExists($to);

        if($exits) {

            if(UserHandler::isFollowing($this->loggedUser->id, $to)) {
                //Parar de seguir
                UserHandler::unfollow($this->loggedUser->id, $to);
            } else {
                //Seguir
                UserHandler::follow($this->loggedUser->id, $to);
            }
        }
        //volta para a página do usuário que está visualizando
        $this->redirect('/perfil/'.$to);
    }

    public function friends($atts = []) {

        //detectanto o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        }

        //pegando informações do usuário
        $user = UserHandler::getUser($id, true);
        if($user == false) {
            $this->redirect('/');
        }

        //verificar se eu sigo o usuário
        $isFollowing = false;

        if($user->id != $this->loggedUser->id){
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile_friends', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
    }

    public function photos($atts = []) {

        //detectanto o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        }

        //pegando informações do usuário
        $user = UserHandler::getUser($id, true);
        if($user == false) {
            $this->redirect('/');
        }

        //verificar se eu sigo o usuário
        $isFollowing = false;

        if($user->id != $this->loggedUser->id){
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile_photos', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'isFollowing' => $isFollowing
        ]);
    }
}