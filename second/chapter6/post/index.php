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
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
echo '★$page';
var_dump($page);

// 最終ページを取得する
$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
echo '★$maxPage';
var_dump($maxPage);

$start = ($page - 1) * 5;
echo '★$start';
var_dump($start);

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?, 5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

// 返信の場合
if (isset($_GET['res'])) {
    $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array(
        $_GET['res'],
    ));
    $table = $response->fetch();
    $message = $table['message'] . '@' . $table['name'] . ' > ';
}

// htmlspecialchars
function h($value) {
    return htmlspecialchars($value, ENT_QUOTES);
}

// 本文内のURLにリンクを設定する
function makeLink($value) {
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
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

      <div style="text-align:right;">
        <a href="logout.php">ログアウト</a>
      </div>

      <form action="" method="post">
        <dl>
          <dt><?php if(isset($member['name'])) {echo h($member['name']);} ?>さん、メッセージを入力してください</dt>
          <dd>
            <textarea name="message" id="" cols="50" rows="5"><?php if (isset($message)) {echo h($message);} ?></textarea>
            <input type="hidden" name="reply_post_id" value="<?php if (isset($_GET['res'])) {echo h($_GET['res']);} ?>">
          </dd>
        </dl>
        <div>
          <input type="submit" value="投稿する">
        </div>
      </form>
      <?php if (isset($posts)) {foreach ($posts as $post) : ?>
      <div class="msg">
        <img src="member_picture/<?php if(isset($post['picture'])) {echo h($post['picture']);} ?>" alt="<?php if(isset($post['name'])) {echo h($post['name']);} ?>" width="48" height="48">
        <p>
          <?php if(isset($post['message'])) {echo makeLink(h($post['message']));} ?><span class="name">（<?php if (isset($post['name'])) {echo h($post['name']);} ?>）</span>
          <span class="reply">[<a href="index.php?res=<?php if (isset($post['id'])) {echo h($post['id']);}?>">Re</a>]</span>
        </p>
        <p class="day">
          <?php if (isset($post['created'])) {echo h($post['created']);} ?>
        </p>
        <p class="more">
          <a href="view.php?id=<?php if (isset($post['id'])) {echo h($post['id']);} ?>">▼</a>
        </p>
        <?php if (isset($post['reply_post_id'])) {if ($post['reply_post_id'] > 0) : ?>
        <p class="motomessage">
          <a href="view.php?id=<?php echo h($post['reply_post_id']); ?>">返信元のメッセージ</a>
        </p>
        <?php endif;} ?>
        <?php if ($_SESSION['id'] === $post['member_id']) : ?>
        <p class="delete">
          [<a href="delete.php?id=<?php if (isset($post['id'])) {echo h($post['id']);} ?>" style="color:#f33;">削除</a>]
        </p>
        <?php endif; ?>
      </div>
      <?php endforeach;} ?>

      <ul class="paging">
        <?php if ($page > 1) : ?>
        <li><a href="index.php?page=<?php echo ($page - 1); ?>">前のページへ</a></li>
        <?php else : ?>
        <li>前のページへ</li>
        <?php endif; ?>
        <?php if ($page < $maxPage) : ?>
        <li><a href="index.php?page=<?php echo ($page + 1); ?>">次のページへ</a></li>
        <?php else : ?>
        <li>次のページへ</li>
        <?php endif; ?>
      </ul>

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