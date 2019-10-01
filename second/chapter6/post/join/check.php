<?php
session_start();
require('../dbconnect.php');

if (!isset($_SESSION['join'])) {
  header('Location: index.php');
  exit();
}

// ノーイメージ画像を指定するテスト
$extension = substr($_SESSION['join']['image'], -3);
if ($extension !== 'jpg' && $extension !== 'gif' && $extension !== 'png') {
  $_SESSION['join']['image'] = 'no-image.png';
}

if (!empty($_POST)) {
  // 登録処理をする
  $statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, picture=?, created=NOW()');
  echo $ret = $statement->execute(array(
    $_SESSION['join']['name'],
    $_SESSION['join']['email'],
    sha1($_SESSION['join']['password']),
    $_SESSION['join']['image'],
  ));
  unset($_SESSION['join']);

  header('Location: thanks.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="../style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>会員登録</h1>
    </div>
    <div id="content">
      <form action="" method="post">
        <input type="hidden" name="action" value="submit">
        <dl>
          <dt>ニックネーム</dt>
          <dd>
            <?php if (isset($_SESSION['join'])) {echo htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES);} ?>
          </dd>
          <dt>メールアドレス</dt>
          <dd>
            <?php if (isset($_SESSION['join'])) {echo htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES);} ?>
          </dd>
          <dt>パスワード</dt>
          <dd>【表示されません】</dd>
          <dt>写真など</dt>
          <dd>
            <img src="../member_picture/<?php if (isset($_SESSION['join'])) {echo htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES);} ?>" alt="アイコン画像" width="100" height="100">
          </dd>
        </dl>
        <div>
          <a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a>｜
          <input type="submit" value="登録する">
        </div>
      </form>
    </div>

  </div>

<?php
// 確認用
if (isset($image)) {
  echo '↓$image';
  var_dump($image);
}

if (isset($error)) {
  echo '↓$error';
  var_dump($error);
}

echo '↓$_FILES';
var_dump($_FILES);

echo '↓$_GET';
var_dump($_GET);

echo '↓$_POST';
var_dump($_POST);

echo '↓$_SESSION';
var_dump($_SESSION);
?>
</body>

</html>