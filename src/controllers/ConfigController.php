<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;


class ConfigController extends Controller
{

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHandler::checklogin();
        if ($this->loggedUser === false) {
            $this->redirect('/login');
        }
    }

    public function index()
    {

        $userInfo = UserHandler::getUser($this->loggedUser->id, false);

        //somente carrega a página de configuração
        $this->render('config', [
            'loggedUser' => $this->loggedUser,
            'userInfo' => $userInfo
        ]);
    }

    //atualiza os dados do usuário
    public function updateAction()
    {
        //receba
        $name = filter_input(INPUT_POST, 'name');
        $birthdate = filter_input(INPUT_POST, 'birthdate');
        $email = filter_input(INPUT_POST, 'email');
        $city = filter_input(INPUT_POST, 'city');
        $work = filter_input(INPUT_POST, 'work');
        $newpass1 = filter_input(INPUT_POST, 'newpass1');
        $newpass2 = filter_input(INPUT_POST, 'newpass2');

        //nome
        if (!$name) {
            $_SESSION['flash'] = 'Campo nome não pode ser vazio!';
            $name = false;
            $this->redirect('/config');
        }
        if (strlen($name) < 5) {
            $_SESSION['flash'] = 'Nome deve ter pelo menos 5 caracteres!';
            $name = false;
            $this->redirect('/config');
        }

        //cidade
        if (!$city) {
            $_SESSION['flash'] = 'Campo cidade não pode ser vazio!';
            $city = false;
            $this->redirect('/config');
        }
        if ($city) {
            if (strlen($city) < 5) {
                $_SESSION['flash'] = 'Campo cidade deve ter pelo menos 5 caracteres!';
                $city = false;
                $this->redirect('/config');
            }
        }

        //trabalho
        if (!$work) {
            $_SESSION['flash'] = 'Campo trabalho não pode ser vazio!';
            $work = false;
            $this->redirect('/config');
        }
        if ($work) {
            if (strlen($work) < 5) {
                $_SESSION['flash'] = 'Campo trabalho deve ter pelo menos 5 caracteres!';
                $work = false;
                $this->redirect('/config');
            }
        }

        //email
        if ($email) {
            //verificar se o conteudo recebido é um email válido
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['flash'] = 'Utilize um formato de email válido!';
                $email = false;
                $this->redirect('/config');
            }

            $userEmail = UserHandler::getEmailById($this->loggedUser->id);

            if (UserHandler::emailExists($email) && $email != $userEmail) {
                $_SESSION['flash'] = 'Email consta no banco de dados!';
                $email = false;
                $this->redirect('/config');
            }
        } else {
            $email = false;
        }

        //data
        if ($birthdate) {
            $newBirthdate = explode('/', $birthdate);
     
            //verificar se realmente é uma data valida
            if(!checkdate($newBirthdate[1],$newBirthdate[0],$newBirthdate[2])) {
                $_SESSION['flash'] = 'Campo data com formato inválido!';
                $birthdate = false;
                $this->redirect('/config');
            }
     
            $birthdate = $newBirthdate[2] . '-' . $newBirthdate[1] . '-' . $newBirthdate[0];

        } else {
            $_SESSION['flash'] = 'Campo data não pode ser vazio!';
            $birthdate = false;
            $this->redirect('/config');
        }

        //senha
        if ($newpass1 != '' || $newpass1 != '') {
            if ($newpass1 != $newpass2) {
                $_SESSION['flash'] = 'Senhas digitadas não coincidem!';
                $newpass1 = false;
                $this->redirect('/config');
            }
            //se chegou até aqui a senha é atualizada
        }

        UserHandler::updateUser($this->loggedUser->id, $name, $birthdate, $email, $city, $work, $newpass1);

        $this->redirect('/config');
    }
}
