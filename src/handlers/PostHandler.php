<?php

namespace src\handlers;

use src\models\Post;
use src\models\User;
use src\models\UserRelation;
use src\models\PostLike;
use src\models\PostComment;

class PostHandler
{

    public static function addPost($idUser, $type, $body)
    {
        if (!empty($idUser)) {
            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }
    }

    public static function delete($idPost, $loggedUserId)
    {
        //1. verificar se o post existe e se o post é seu
        $post = Post::select()
            ->where('id', $idPost)
            ->where('id_user', $loggedUserId)
            ->get();

        if (count($post) > 0) {
            $post = $post[0];
            //2. deletar o likes e comments 
            PostLike::delete()->where('id_post', $idPost)->execute();
            PostComment::delete()->where('id_post', $idPost)->execute();

            //3. se a foto for type photo e deletar também
            if ($post['type'] == 'photo') {
                $img = __DIR__.'/../../public/media/uploads/' . $post['body'];
                if (file_exists($img)) {
                    unlink($img);
                }
            }
            //4. deletar o post
            Post::delete()->where('id', $idPost)->execute();
        }
    }

    public static function _postListToObject($postList, $loggedUserId)
    {
        $posts = [];
        foreach ($postList as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false;

            if ($postItem['id_user'] == $loggedUserId) {
                $newPost->mine = true;
            }

            //4. preencher as informações adicionais mo post
            $newUser = User::select()->where('id', $postItem['id_user'])->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];

            //todo: 4.1 preencher informações de like
            $likes = PostLike::select()->where('id_post', $postItem['id'])->get();
            $newPost->likeCount = count($likes);
            $newPost->liked = self::isLiked($postItem['id'], $loggedUserId);

            //todo: 4.2 preencher informações de comments
            $newPost->comments = PostComment::select()->where('id_post', $postItem['id'])->get();

            foreach ($newPost->comments as $key => $comment) {
                $newPost->comments[$key]['user'] = User::select()->where('id', $comment['id_user'])->one();
            }
            $posts[] = $newPost;
        }

        return $posts;
    }

    public static function isLiked($id, $loggedUserId)
    {
        $myLike = PostLike::select()
            ->where('id_post', $id)
            ->where('id_user', $loggedUserId)
            ->get();

        if (count($myLike) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function deleteLike($id, $loggedUserId)
    {
        PostLike::delete()
            ->where('id_post', $id)
            ->where('id_user', $loggedUserId)
            ->execute();
    }

    public static function insertLike($id, $loggedUserId)
    {
        PostLike::insert([
            'id_post' => $id,
            'id_user' => $loggedUserId,
            'created_at' => date('Y-m-d H:i:s')
        ])->execute();
    }

    public static function addComment($id, $txt, $loggedUserId)
    {
        PostComment::insert([
            'id_post' => $id,
            'id_user' => $loggedUserId,
            'created_at' => date('Y-m-d H:i:s'),
            'body' => $txt
        ])->execute();
    }

    public static function getUserFeed($idUser, $page, $loggedUserId)
    {
        $perPage = 2;

        //pegar os posts dos usuários
        $postList = Post::select()
            ->where('id_user', $idUser)
            ->orderBy('created_at', 'desc')
            ->page($page, $perPage)
            ->get();

        $total = Post::select()
            ->where('id_user', $idUser)
            ->count();

        $pageCount = ceil($total / $perPage);

        //3. transformar os resultados em objetos nos models

        $posts = self::_postListToObject($postList, $loggedUserId);

        //5. retornar o resultado

        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];
    }

    public static function getHomeFeed($idUser, $page)
    {
        $perPage = 2;

        //1. pegar lista de usuarios que eu estou seguindo
        $userList = UserRelation::select()
            ->where('user_from', $idUser)
            ->get();
        $users = [];
        foreach ($userList as $userItem) {
            $users[] = $userItem['user_to'];
        }
        $users[] = $idUser; //add eu porque eu vejo as minhas proprias postagens

        //2. pegar os posts da galera ordenada por data
        $postList = Post::select()
            ->where('id_user', 'in', $users)
            ->orderBy('created_at', 'desc')
            ->page($page, $perPage)
            ->get();

        $total = Post::select()
            ->where('id_user', 'in', $users)
            ->count();

        $pageCount = ceil($total / $perPage);

        //3. transformar os resultados em objetos nos models
        $posts = self::_postListToObject($postList, $idUser);

        //5. retornar o resultado

        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];
    }

    public static function getPhotosFrom($idUser)
    {
        $photosData = Post::select()
            ->where('id_user', $idUser)
            ->where('type', 'photo')
            ->get();

        $photos = [];

        foreach ($photosData as $photo) {
            $newPost = new Post();
            $newPost->id = $photo['id'];
            $newPost->type = $photo['type'];
            $newPost->created_at = $photo['created_at'];
            $newPost->body = $photo['body'];

            $photos[] = $newPost;
        }
        return $photos;
    }
}
