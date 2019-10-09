<?php
session_start();
require('dbconnect.php');

function h($str) {
  echo htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if (isset($_COOKIE['email']) && $_COOKIE['email'] !== '') {
    $email = $_COOKIE['email'];
}

if (!empty($_POST)) {
    $email = $_POST['email'];

    if ($_POST['email'] !== '' && $_POST['password'] !== '') {
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
        $login->execute(array(
            $_POST['email'],
            sha1($_POST['password']),
        ));
        $member = $login->fetch();

        if ($member) {
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

            if ($_POST['save'] === 'on') {
              setcookie('email', $_POST['email'], time() + 60 * 10);
            }

            header('Location: index.php');
            exit();
        } else {
            $error['login'] = 'failed';
        }
    } else {
        $error['login'] = 'blank';
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="style.css" />
  <title>Login</title>
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>Login</h1>
      <p class="logout"><a href="join/">Sign up</a></p>
    </div>
    <div id="content">
      <?php if (isset($error['login'])) : ?>
        <?php if ($error['login'] === 'blank') : ?>
          <p class="error">メールアドレスとパスワードを入力してください</p>
        <?php endif; ?>
        <?php if ($error['login'] === 'failed') : ?>
          <p class="error">ログインに失敗しました</p>
        <?php endif; ?>
      <?php endif; ?>
      <form action="" method="post">
        <dl>
          <dt>email address</dt>
          <dd>
            <input type="text" name="email" size="35" maxlength="255" value="<?php if (isset($email)) {h($email);} ?>" />
          </dd>
          <dt>set a password</dt>
          <dd>
            <input type="password" name="password" size="35" maxlength="255" value="" />
          </dd>
          <dt class="hidden">ログイン情報の記録</dt>
          <dd class="mini">
            <input id="save" type="checkbox" name="save" value="on">
            <label for="save">Stey signed in</label>
          </dd>
        </dl>
        <div>
          <input class="button" type="submit" value="Login" />
        </div>
      </form>
      <p class="copy"><img src="images/txt_copyright.png" width="136" height="15" alt="(C) H2O Space. MYCOM" /></p>
    </div>
  </div>
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