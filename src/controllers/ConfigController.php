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
        $updateFields = [];

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
        $updateFields['name'] = $name;

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
        $updateFields['city'] = $city;

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
        $updateFields['work'] = $work;

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
        $updateFields['email'] = $email;

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
        $updateFields['birthdate'] = $birthdate;

        //senha
        if ($newpass1 != '' || $newpass2 != '') {
            if ($newpass1 != $newpass2) {
                $_SESSION['flash'] = 'Senhas digitadas não coincidem!';
                $newpass1 = false;
                $this->redirect('/config');
            }
            //se chegou até aqui a senha é atualizada
        }
        $updateFields['password'] = $newpass1;

        // AVATAR
        if(isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
            $newAvatar = $_FILES['avatar'];

            if(in_array($newAvatar['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
                $avatarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
                $updateFields['avatar'] = $avatarName;
            }
        }

        // COVER
        if(isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])) {
            $newCover = $_FILES['cover'];

            if(in_array($newCover['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
                $coverName = $this->cutImage($newCover, 850, 310, 'media/covers');
                $updateFields['cover'] = $coverName;
            }
        }

        UserHandler::updateUser($updateFields, $this->loggedUser->id);

        $this->redirect('/config');
    }

    private function cutImage($file, $w, $h, $folder){
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
        $ratio = $widthOrig / $heightOrig;

        $newWidth = $w;
        $newHeight = $newWidth / $ratio;

        if($newHeight < $h) {
            $newHeight = $h;
            $newWidth = $newHeight * $ratio;
        }

        $x = $w - $newWidth;
        $y = $h - $newHeight;
        $x = $x < 0 ? $x / 2 : $x;
        $y = $y < 0 ? $y / 2 : $y;

        $finalImage = imagecreatetruecolor($w, $h);
        switch($file['type']) {
            case 'image/jpeg':
            case 'igame/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
            break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
            break;
        }

        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0, 0,
            $newWidth, $newHeight, $widthOrig, $heightOrig
        );

        $fileName = md5(time().rand(0,9999)).'.jpg';

        imagejpeg($finalImage, $folder.'/'.$fileName);

        return $fileName;
    }
}
