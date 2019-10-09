<?php
session_start();
require('dbconnect.php');

if (empty($_REQUEST['id'])) {
  header('Location: index.php');
  exit();
}

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');
$posts->execute(array(
  $_REQUEST['id'],
));
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>掲示板</title>

  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>掲示板</h1>
    </div>
    <div id="content">
      <p>&laquo;<a href="index.php">一覧にもどる</a></p>

    <?php if ($post = $posts->fetch()): ?>
      <div class="msg">
        <img src="member_picture/<?php print(htmlspecialchars($post['picture'], ENT_QUOTES)); ?>" />
        <p><?php print(htmlspecialchars($post['message'], ENT_QUOTES)); ?><span class="name">（<?php print(htmlspecialchars($post['name'], ENT_QUOTES)); ?>）</span></p>
        <p class="day"><?php print(htmlspecialchars($post['created'], ENT_QUOTES)); ?></p>
      </div>
    <?php else: ?>
      <p>この投稿は削除されたか、URLが間違っています</p>
    <?php endif; ?>
    </div>
    <!-- 確認用 -->
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