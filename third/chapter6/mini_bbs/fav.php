<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
    $usrId = $_REQUEST['usr'];
    $postId = $_REQUEST['post'];

    $favorites = $db->prepare('SELECT * FROM favorites WHERE member_id=? AND post_id=?');
    $favorites->execute(array(
        $usrId,
        $postId,
    ));
    $favorite = $favorites->fetch();

    if (empty($favorite)) {
        // いいね追加
        $addFav = $db->prepare('INSERT INTO favorites SET member_id=?, post_id=?, fav_score=1');
        $addFav->execute(array(
            $usrId,
            $postId,
        ));
    } else {
        // いいねを取り消す
        $deleteFav = $db->prepare('DELETE FROM favorites WHERE member_id=? AND post_id=?');
        $deleteFav->execute(array(
            $usrId,
            $postId,
        ));
    }
}

$page = urlencode($_REQUEST['page']);

header('Location: index.php?page=' . $page);
exit();
