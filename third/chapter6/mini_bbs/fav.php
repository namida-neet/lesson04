<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
    $postId = $_REQUEST['post'];
    $usrId = $_REQUEST['usr'];

    $favorites = $db->prepare('SELECT * FROM favorites WHERE member_id=? AND post_id=?');
    $favorites->execute(array(
        $usrId,
        $postId,
    ));
    $favorite = $favorites->fetch();

    if (empty($favorite)) {
        // いいね追加
        $addFav = $db->prepare('INSERT INTO favorites SET member_id=?, post_id=?, score=1');
        $addFav->execute(array(
            $usrId,
            $postId,
        ));
    } else {
        // いいねを取り消す
        $delFav = $db->prepare('DELETE FROM favorites WHERE member_id=? AND post_id=?');
        $delFav->execute(array(
            $usrId,
            $postId,
        ));
    }
}
header('Location: index.php');
exit();

echo '★$_POST';
var_dump($_POST);
echo '★$_GET';
var_dump($_GET);
echo '★$_COOKIE';
var_dump($_COOKIE);
echo '★$_SESSION';
var_dump($_SESSION);
echo '★$_FILES';
var_dump($_FILES);
if (isset($error)) {
  echo '★$error';
  var_dump($error);
}
