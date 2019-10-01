<?php
session_start();
require('dbconnect.php');

if (empty($_GET['id'])) {
    header('Location: index.php');
}

// 投稿を取得する
$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
$posts->execute(array(
    $_GET['id'],
));
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>ひとこと掲示板</h1>
    </div>
    <div id="content">

      <p>&laquo;<a href="index.php">一覧に戻る</a></p>
      <?php if ($post = $posts->fetch()) : ?>
      <div class="msg">
        <img src="member_picture/<?php if (isset($post['picture'])) {echo htmlspecialchars($post['picture'], ENT_QUOTES);} ?>" alt="<?php if (isset($post['name'])) {echo htmlspecialchars($post['name'], ENT_QUOTES);} ?>" width="48" height="48">
        <p>
          <?php if (isset($post['message'])) {echo htmlspecialchars($post['message'], ENT_QUOTES);} ?>
          <span class="name">（<?php if (isset($post['name'])) {echo htmlspecialchars($post['name'], ENT_QUOTES);} ?>）</span>
        </p>
        <p class="day">
          <?php if ($post['created']) {echo htmlspecialchars($post['created'], ENT_QUOTES);} ?>
        </p>
      </div>
      <?php else : ?>
      <p>この投稿は削除されたか、URLが間違っています</p>
      <?php endif; ?>

    </div>
  </div>

<?php
// 確認用
echo '★↓time()';
var_dump(time());

if (isset($members)) {
  echo '★↓$members';
  var_dump($members);
}

if (isset($member)) {
  echo '★↓$member';
  var_dump($member);
}

if (isset($message)) {
  echo '★↓$message';
  var_dump($message);
}

if (isset($post)) {
  echo '★↓$post';
  var_dump($post);
}

if (isset($table)) {
  echo '★↓$table';
  var_dump($table);
}

if (isset($error)) {
  echo '★↓$error';
  var_dump($error);
}

echo '★↓$_FILES';
var_dump($_FILES);

echo '★↓$_GET';
var_dump($_GET);

echo '★↓$_POST';
var_dump($_POST);

echo '★↓$_SESSION';
var_dump($_SESSION);

echo '★↓$_COOKIE';
var_dump($_COOKIE);
?>
</body>

</html>