<?php
session_start();
require('dbconnect.php');
require('htmlspecialchars.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 60 * 10 > time()) {
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array(
        $_SESSION['id'],
    ));
    $member = $members->fetch();
} else {
    header('Location: login.php');
    exit();
}

if (!empty($_POST) && $_POST['message'] !== '') {
    if ($_POST['reply_post_id'] === '') {
        $_POST['reply_post_id'] = NULL;
    }
    $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_message_id=?, created=NOW()');
    $message->execute(array(
        $member['id'],
        $_POST['message'],
        $_POST['reply_post_id'],
    ));

    header('Location: index.php');
    exit();
}

if (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
    $page = $_REQUEST['page'];
} else {
    $page = 1;
}
$page = max($page, 1);

$counts = $db->query('SELECT COUNT(*) AS count FROM posts');
$count = $counts->fetch();
$maxPage = ceil($count['count'] / 2);
$page = min($page, $maxPage);

$start = ($page - 1) * 2;

// ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ここにいいねと星について書いていきます↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

// $posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');
// $posts->bindParam(1, $start, PDO::PARAM_INT);
// $posts->execute();

// $posts = $db->prepare('SELECT m.name, m.picture, p.*, COUNT(f.fav_score) AS favCount FROM members m, posts p LEFT JOIN favorites f ON p.id = f.post_id WHERE m.id = p.member_id GROUP BY p.id ORDER BY p.created DESC LIMIT ?, 2;');
// $posts->bindParam(1, $start, PDO::PARAM_INT);
// $posts->execute();

$posts = $db->prepare('SELECT m.name, m.picture, p.*, COUNT(f.fav_score) AS favCount, AVG(s.star_score) AS starAverage FROM members m, posts p LEFT JOIN favorites f ON p.id = f.post_id LEFT JOIN stars s ON p.id = s.post_id WHERE m.id = p.member_id GROUP BY p.id ORDER BY p.created DESC LIMIT ?, 2;');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

// ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ここにいいねと星について書いていきます↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

if (isset($_REQUEST['res'])) {
    // 返信の処理
    $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');
    $response->execute(array(
        $_REQUEST['res'],
    ));

    $table = $response->fetch();
    $message = '@' . $table['name'] . ' ' . $table['message'] . "\n" . '> ';
}

// ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ここにリツイートについて書いていきます↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

if (isset($_GET['repost'])) {
    // リポストするもとの投稿を取得する
    $repostGetPost = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');
    $repostGetPost->execute(array(
        $_GET['repost'],
    ));
    $repostTable = $repostGetPost->fetch();
    $repostMessage = '[リポスト] ' . $repostTable['message'] . ' by ' . $repostTable['name'];

    // リポストを投稿する
    $addRepost = $db->prepare('INSERT INTO posts SET member_id=?, message=?, repost_message_id=?, created=NOW()');
    $addRepost->execute(array(
        $member['id'],
        $repostMessage,
        $_GET['repost'],
    ));
    header('Location: index.php');
    exit();
}

// ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ここにリツイートについて書いていきます↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>BBS</title>
  <link rel="stylesheet" href="style.css" />
  <script src="https://kit.fontawesome.com/ccf5e700a2.js" crossorigin="anonymous"></script>
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>BBS</h1>
      <div class="header-button""><a href="logout.php">Logout</a></div>
    </div>

    <div id="content">
      <div class="post-area">
        <div class="user-info">
          <p class="user-icon">
            <img src="member_picture/<?php h($member['picture']); ?>" width="48" height="48" alt="<?php h($member['name']); ?>のアイコン">
          </p>
          <p class="user-name">
            <?php h($member['name']); ?>
          </p>
        </div><!-- user-info -->

        <form class="bbs-form" action="" method="post">
          <textarea class="bbs-textarea" name="message" cols="50" rows="5"><?php if (isset($message)) { h($message); } ?></textarea>
          <input type="hidden" name="reply_post_id" value="<?php if (isset($_REQUEST['res'])) { h($_REQUEST['res']); } ?>">
          <input class="submit-button -bbs-message" type="submit" value="post">
        </form>
      </div><!-- post-area -->

      <?php foreach ($posts as $post) : ?>
      <div class="msg">
        <img src="member_picture/<?php h($post['picture']); ?>" width="48" height="48" alt="<?php h($post['name']); ?>のアイコン">
        <p class="post-message">
          <?php h($post['message']); ?>
          <span class="name">（<?php h($post['name']); ?>）</span>
          <span class="post-number">[No.<?php h($post['id']); ?>]</span>
        </p>

        <div class="post-description">

          <p class="day">
            <a href="view.php?id=<?php h($post['id']); ?>&page=<?php h($page); ?>"><?php h($post['created']); ?></a>
          </p>

          <?php if ($post['reply_message_id'] > 0) : ?>
          <p class="reply_message">
            <a href="view.php?id=<?php h($post['reply_message_id']); ?>&page=<?php h($page); ?>">返信元のメッセージ</a>
          </p>
          <?php endif; ?>

        </div><!-- post-description -->
        <div class="reaction-tools">

          <p class="res-button">
            <a href="index.php?res=<?php h($post['id']); ?>">Reply</a>
          </p>

<!-- ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ここにリツイートについて書いていきます↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ -->

          <p class="res-button">
            <a href="index.php?repost=<?php h($post['id']); ?>&user=<?php h($member['id']); ?>">Repost</a>
          </p>

<!-- ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ここにリツイートについて書いていきます↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ -->

<!-- ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ここにいいねボタンについて書いていきます↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ -->

          <p class="favorite">
            <a href="fav.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>">
              <?php
              $favCheck = $db->prepare('SELECT * FROM favorites WHERE member_id=? AND post_id=?');
              $favCheck->execute(array(
                  $_SESSION['id'],
                  $post['id'],
              ));
              $heartIcon = $favCheck->fetch();

              if (!empty($heartIcon)): ?>
              <i class="fas fa-heart"></i>
              <?php else: ?>
              <i class="far fa-heart"></i>
              <?php endif; ?>
            </a>
          </p>
          <p class="favCount">
            <?php h($post['favCount']); ?>
          </p>

<!-- ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ここにいいねボタンについて書いていきます↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ -->

<!-- ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ここに星ボタンについて書いていきます↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓ -->

          <p class="star">
            <?php
            $starCheck = $db->prepare('SELECT * FROM stars WHERE member_id=? AND post_id=?');
            $starCheck->execute(array(
                $_SESSION['id'],
                $post['id'],
            ));
            $starIcon = $starCheck->fetch();

            if (empty($starIcon)): ?>
            <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=1"><i class="far fa-smile"></i></a>
            <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=2"><i class="far fa-laugh-beam"></i></a>
            <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=3"><i class="far fa-grin-squint-tears"></i></a>
            <?php else: ?>
              <?php if ($starIcon['star_score'] === "1"): ?>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=0"><i class="fas fa-minus-circle"></i></a>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=1"><i class="far fa-smile star-icon-color"></i></a>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=2"><i class="far fa-laugh-beam"></i></a>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=3"><i class="far fa-grin-squint-tears"></i></a>
              <?php elseif ($starIcon['star_score'] === "2"): ?>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=0"><i class="fas fa-minus-circle"></i></a>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=1"><i class="far fa-smile star-icon-color"></i></a>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=2"><i class="far fa-laugh-beam star-icon-color"></i></a>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=3"><i class="far fa-grin-squint-tears"></i></a>
              <?php elseif ($starIcon['star_score'] === "3"): ?>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=0"><i class="fas fa-minus-circle"></i></a>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=1"><i class="far fa-smile star-icon-color"></i></a>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=2"><i class="far fa-laugh-beam star-icon-color"></i></a>
              <a href="star.php?user=<?php h($member['id']); ?>&post=<?php h($post['id']); ?>&page=<?php h($page); ?>&score=3"><i class="far fa-grin-squint-tears star-icon-color"></i></a>
              <?php endif; ?>
            <?php endif; ?>
          </p>
          <p class="starAverage">
            <?php h(round($post['starAverage'], 1)); ?>
          </p>

<!-- ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ここに星ボタンについて書いていきます↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑ -->

          <?php if ($_SESSION['id'] === $post['member_id']): ?>
          <p class="delete-button">
            <a href="delete.php?id=<?php h($post['id']); ?>">削除</a>
          </p>
          <?php endif; ?>

        </div><!-- reaction-tools -->
      </div><!-- msg -->
      <?php endforeach; ?>

      <ul class="paging">
        <?php if ($page > 1): ?>
        <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
        <?php else: ?>
        <li>前のページへ</li>
        <?php endif; ?>

        <li>｜<a class="uppercase" href="index.php">top</a>｜</li>

        <?php if ($page < $maxPage): ?>
        <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
        <?php else: ?>
        <li>次のページへ</li>
        <?php endif; ?>
      </ul>

    </div><!-- #content -->
  </div><!-- #wrap -->
  <div class="var_dump">
    <?php
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
    ?>
  </div>
</body>

</html>