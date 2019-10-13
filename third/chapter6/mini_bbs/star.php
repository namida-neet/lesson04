<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id'])) {
    $userId = $_REQUEST['user'];
    $postId = $_REQUEST['post'];
    $score = $_REQUEST['score'];

    $stars = $db->prepare('SELECT * FROM stars WHERE member_id=? AND post_id=?');
    $stars->execute(array(
        $userId,
        $postId,
    ));
    $star = $stars->fetch();

    if (empty($star)) {
        // 星の点数を追加
        $addStar = $db->prepare('INSERT INTO stars SET member_id=?, post_id=?, star_score=?');
        $addStar->execute(array(
            $userId,
            $postId,
            $score,
        ));
    } else {
        if ($score === "0") {
            // 星を削除する
            $deleteStar = $db->prepare('DELETE FROM stars WHERE member_id=? AND post_id=?');
            $deleteStar->execute(array(
                $userId,
                $postId,
            ));
        } else {
            // 星の点数を変更する
            $changeScore = $db->prepare('UPDATE stars SET star_score=? WHERE member_id=? AND post_id=?');
            $changeScore->execute(array(
                $score,
                $userId,
                $postId,
            ));
        }
    }
}

$page = urlencode($_REQUEST['page']);

header('Location: index.php?page=' . $page);
exit();
