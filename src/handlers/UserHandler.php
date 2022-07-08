<?php
namespace src\handlers;

use src\models\User;
use src\models\UserRelation;
use src\handlers\PostHandler;

class UserHandler {

    public static function checklogin() {
        if(!empty($_SESSION['token'])) { //se não está vazio
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if(count($data) > 0) {

                $loggedUser = new User();
                $loggedUser->id = $data['id'];
                $loggedUser->name = $data['name'];
                $loggedUser->avatar = $data['avatar'];
                
                return $loggedUser;
            } 
        } 
        return false; //e vai para /login
    }

    public static function verifyLogin($email, $password) {
        $user = User::select()->where('email', $email)->one();

        if($user) {
            if(password_verify($password, $user['password'])) {
                $token = md5(time().rand(0,9999).time());

                User::update()
                    ->set('token', $token)
                    ->where('email', $email)
                ->execute();

                return $token;
            }
        }
        return false;
    }

    public static function emailExists($email) {
        $user = User::select()->where('email', $email)->one();
        return $user ? true : false;
    }

    public static function idExists($id) {
        $user = User::select()->where('id', $id)->one();
        return $user ? true : false;
    }

    public static function getUser($id, $full = false) {
        $data = User::select()->where('id', $id)->one();

        if($data) {
            $user = new User;
            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->birthdate = $data['birthdate'];
            $user->city = $data['city'];
            $user->work = $data['work'];
            $user->avatar = $data['avatar'];
            $user->cover = $data['cover'];

            if($full) {
                $user->followers = [];
                $user->following = [];
                $user->photos = [];

                //followers - pessoas que me seguem
                $followers = UserRelation::select()->where('user_to', $id)->get();

                foreach($followers as $follower) {
                    $userData = User::select()->where('id', $follower['user_from'])->one(); //pegar somente o id do usuário que está te seguindo
                    $newUser = new User();
                    $newUser->id = $userData['id']; //da tabela de usuarios
                    $newUser->name = $userData['name'];
                    $newUser->avatar = $userData['avatar'];

                    $user->followers[] = $newUser; //add no array
                }

                //following - pessoas que eu estou seguindo
                $following = UserRelation::select()->where('user_from', $id)->get();

                foreach($following as $follower) {
                    $userData = User::select()->where('id', $follower['user_to'])->one(); //pegar somente o id do usuário que estou seguindo
                    $newUser = new User(); //table usuarios
                    $newUser->id = $userData['id']; //da tabela de usuarios
                    $newUser->name = $userData['name']; 
                    $newUser->avatar = $userData['avatar'];

                    $user->following[] = $newUser; //add no array
                }

                //photos
                
                $user->photos = PostHandler::getPhotosFrom($id);

            }

            return $user;
        }

        return false;
    }

    public static function getEmailById($id) {
        $data = User::select()->where('id', $id)->one();

        if($data) {
            return $data['email'];
        }

        return false;
    }
    
    public static function addUser($name, $email, $password, $birthdate) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = md5(time().rand(0,9999).time());


        User::insert([
            'email' => $email,
            'password' => $hash,
            'name' => $name,
            'birthdate' => $birthdate,
            'token' => $token
        ])->execute();

        return $token;
    }

    public static function isFollowing($from, $to){
        $data = UserRelation::select()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->one();

        if($data) {
            return true;
        } else {
            return false;
        }
    }

    public static function follow($from, $to) {
        UserRelation::insert([
            'user_from' => $from,
            'user_to' => $to
        ])->execute();
    }

    public static function unfollow($from, $to) {
        UserRelation::delete()
            ->where('user_from', $from)
            ->where('user_to', $to)
        ->execute();
    }

    public static function searchUser($term) {
        $users = [];

        $data = User::select()->where('name', 'like', '%'.$term.'%')->get();

        if(!empty($data)) {
            foreach($data as $user) {
                $newUser = new User();
                $newUser->id = $user['id'];
                $newUser->name = $user['name'];
                $newUser->avatar = $user['avatar'];
    
                $users[] = $newUser;
            }
        }

        return $users;
    }

    public static function updateUser($field, $id) {

        //vai efetuar a troca somente em itens que ele recebeu valor diferente de false
        
        ($field['name'])?User::update()->set('name', $field['name'])->where('id', $id)->execute():"";
        ($field['birthdate'])?User::update()->set('birthdate', $field['birthdate'])->where('id', $id)->execute():"";
        ($field['email'])?User::update()->set('email', $field['email'])->where('id', $id)->execute():"";
        ($field['city'])?User::update()->set('city', $field['city'])->where('id', $id)->execute():"";
        ($field['work'])?User::update()->set('work', $field['work'])->where('id', $id)->execute():"";

        if($field['password']) {
            $hash = password_hash($field['password'], PASSWORD_DEFAULT);
            User::update()->set('password', $hash)->where('id', $id)->execute();
        }
    }

}