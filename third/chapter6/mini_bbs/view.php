<?php
session_start();
require('dbconnect.php');
require('htmlspecialchars.php');

if (empty($_REQUEST['id'])) {
    header('Location: index.php');
    exit();
}

$page = $_REQUEST['page'];

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
  <title>View</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>View</h1>
      <div class="header-button"><a href="logout.php">Logout</a></div>
    </div>

    <div id="content">
      <?php if ($post = $posts->fetch()): ?>
      <div class="msg">
        <img src="member_picture/<?php h($post['picture']); ?>">
        <p><?php h($post['message']); ?><span class="name">（<?php h($post['name']); ?>）</span></p>
        <p class="day"><?php h($post['created']); ?></p>
      </div><!-- msg -->
      <?php else: ?>
      <p>この投稿は削除されたか、URLが間違っています</p>
      <?php endif; ?>
      <p><a class="cancel-button" href="index.php?page=<?php h($page); ?>">Return</a></p>
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