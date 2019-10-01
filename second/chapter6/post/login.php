<?php
require('dbconnect.php');

session_start();

if (isset($_COOKIE['email']) && isset($_POST['email']) && isset($_POST['password'])) {
    if ($_COOKIE['email'] !== '') {
        $_POST['email'] = $_COOKIE['email'];
        $_POST['password'] = $_COOKIE['password'];
        $_POST['save'] = 'on';
    }
}

if (!empty($_POST)) {
  // ログインの処理
  if (isset($_POST['email']) && isset($_POST['password'])) {
      if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
        $login->execute(array(
          $_POST['email'],
          sha1($_POST['password']),
        ));
        $member = $login->fetch();
      }
  }

  if (isset($member)) {
      if($member){
          // ログイン成功
          $_SESSION['id'] = $member['id'];
          $_SESSION['time'] = time();

          // ログイン情報を記録する
          if (isset($_POST['save'])) {
              if ($_POST['save'] === 'on') {
                  setcookie('email', $_POST['email'], time() + 60 * 60 * 24 * 14);
                  setcookie('password', $_POST['password'], time() + 60 * 60 * 24 * 14);
              }
          }

          header('Location: index.php');
          exit();
      } else {
        $error['login'] = 'failed';
      }
  }
} else {
    $error['login'] = 'blank';
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
      <h1>ログインする</h1>
    </div>
    <div id="content">

      <div id="lead">
        <p>メールアドレスとパスワードを入力してログインしてください</p>
        <p>入会手続きがまだの方は&raquo;<a href="join/">入会手続きをしてください</a></p>
      </div>
      <form action="" method="post">
        <dl>
          <dt>メールアドレス</dt>
          <dd>
            <input type="text" name="email" size="35" maxlegth="255" value="<?php if (isset($_POST['email'])) {echo htmlspecialchars($_POST['email'], ENT_QUOTES);} ?>">
            <?php if (isset($error['login'])) {if ($error['login'] === 'blank') : ?>
              <p class="error">* メールアドレスとパスワードを入力してください</p>
            <?php endif;} ?>
            <?php if (isset($error['login'])) {if ($error['login'] === 'failed') : ?>
              <p class="error">* ログインに失敗しました</p>
            <?php endif;} ?>

          </dd>
          <dt>パスワード</dt>
          <dd>
            <input type="password" name="password" size="35" maxlength="255" value="<?php if(isset($_POST['password'])) {echo htmlspecialchars($_POST['password'], ENT_QUOTES);} ?>">
          </dd>
          <dt>ログイン情報の記録</dt>
          <dd>
            <input id="save" type="checkbox" name="save" value="on"><label for="save">次回からは自動でログインする</label>
          </dd>
        </dl>
        <div><input type="submit" value="ログインする"></div>
      </form>

    </div>

  </div>

<?php
// 確認用
if (isset($login)) {
  echo '↓$login';
  var_dump($login);
}

if (isset($member)) {
  echo '↓$member';
  var_dump($member);
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

echo '↓$_COOKIE';
var_dump($_COOKIE);
?>
</body>

</html>