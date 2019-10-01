<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 60 * 60 > time()) {
  // ログインしている
  $_SESSION['time'] = time();

  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  // ログインしていない
  header('Location: login.php');
  exit();
}

// 投稿を記録する
if (!empty($_POST)) {
    if ($_POST['message']) {
        if ($_POST['reply_post_id'] === '') {
            $_POST['reply_post_id'] = NULL;
        }
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_post_id=?, created=NOW()');
        $message->execute(array(
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id'],
        ));

        header('Location: index.php');
        exit();
    }
}

// 投稿を取得する
$posts = $db->query('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC');

// 返信の場合
if (isset($_GET['res'])) {
    $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array(
        $_GET['res'],
    ));
    $table = $response->fetch();
    $message = $table['message'] . '@' . $table['name'] . ' > ';
}
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

      <form action="" method="post">
        <dl>
          <dt><?php if(isset($member['name'])) {echo htmlspecialchars($member['name'], ENT_QUOTES);} ?>さん、メッセージを入力してください</dt>
          <dd>
            <textarea name="message" id="" cols="50" rows="5"><?php if (isset($message)) {echo htmlspecialchars($message, ENT_QUOTES);} ?></textarea>
            <input type="hidden" name="reply_post_id" value="<?php if (isset($_GET['res'])) {echo htmlspecialchars($_GET['res'], ENT_QUOTES);} ?>">
          </dd>
        </dl>
        <div>
          <input type="submit" value="投稿する">
        </div>
      </form>
      <?php foreach ($posts as $post) : ?>
      <div class="msg">
        <img src="member_picture/<?php if(isset($post['picture'])) {echo htmlspecialchars($post['picture'], ENT_QUOTES);} ?>" alt="<?php if(isset($oist['name'])) {echo htmlspecialchars($post['name'], ENT_QUOTES);} ?>" width="48" height="48">
        <p>
          <?php if(isset($post['message'])) {echo htmlspecialchars($post['message'], ENT_QUOTES);} ?><span class="name">（<?php if (isset($post['name'])) {echo htmlspecialchars($post['name'], ENT_QUOTES);} ?>）</span>
          <span class="reply">[<a href="index.php?res=<?php if (isset($post['id'])) {echo htmlspecialchars($post['id'], ENT_QUOTES);}?>">Re</a>]</span>
        </p>
        <p class="day"><?php if (isset($post['created'])) {echo htmlspecialchars($post['created'], ENT_QUOTES);} ?></p>
      </div>
      <?php endforeach; ?>

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