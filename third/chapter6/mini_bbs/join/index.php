<?php
session_start();
require('../dbconnect.php');
require('../htmlspecialchars.php');

// 入力内容をチェック
if (!empty($_POST)) {
    if ($_POST['name'] === '') {
        $error['name'] = 'blank';
    }
    if ($_POST['email'] === '') {
        $error['email'] = 'blank';
    }
    if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    if ($_POST['password'] === '') {
        $error['password'] = 'blank';
    }
    $fileName = $_FILES['image']['name'];
    if (!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext !== 'jpg' && $ext !== 'gif' && $ext !== 'png') {
            $error['image'] = 'type';
        }
    }

    // アカウントの重複をチェック
    if (empty($error)) {
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array(
            $_POST['email'],
        ));
        $record = $member->fetch();
        if ($record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
    }

    if (empty($error)) {
        // アイコン画像をアップロードする
        if (empty($fileName)) {
            $image = '100x100.png';
        } else {
            $image = date('YmdHis') . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
        }

        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;

        header('Location: check.php');
        exit();
    }
}

// check.phpから戻ってきたときに入力内容を再現する
if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] === 'rewrite' && isset($_SESSION['join'])) {
        $_POST = $_SESSION['join'];
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Sign up</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>Sign up</h1>
      <p class="header-button"><a href="../login.php"">Login</a></p>
    </div><!-- #head -->

    <div id="content">
      <form class="signup-form" action="" method="post" enctype="multipart/form-data">
        <p class="signup-form__label-name uppercase"><label for="name">name</label><span class="required">mandatory field</span></p>
        <input type="text" name="name" id="name" size="35" maxlength="255" value="<?php if (isset($_POST['name'])) { h($_POST['name']); } ?>">
        <?php if (isset($error['name']) && $error['name'] === 'blank'): ?>
        <p class="error">* ニックネームを入力してください</p>
        <?php endif; ?>

        <p class="signup-form__label-name uppercase"><label for="email">email address</label><span class="required">mandatory field</span></p>
        <input type="text" name="email" id="email" size="35" maxlength="255" value="<?php if (isset($_POST['email'])) { h($_POST['email']); } ?>">
        <?php if (isset($error['email']) && $error['email'] === 'blank'): ?>
        <p class="error">* メールアドレスを入力してください</p>
        <?php endif; ?>
        <?php if (isset($error['email']) && $error['email'] === 'duplicate'): ?>
        <p class="error">* このメールアドレスはすでに登録されています</p>
        <?php endif; ?>

        <p class="signup-form__label-name uppercase"><label for="password">password</label><span class="required">mandatory field</span></p>
        <input type="password" name="password" id="password" size="10" maxlength="20" value="<?php if (isset($_POST['password'])) { h($_POST['password']); } ?>">
        <?php if (isset($error['password']) && $error['password'] === 'blank'): ?>
        <p class="error">* パスワードを入力してください</p>
        <?php endif; ?>
        <?php if (isset($error['password']) && $error['password'] === 'length') : ?>
        <p class="error">* パスワードは4文字以上で入力してください</p>
        <?php endif; ?>

        <p class="signup-form__label-name uppercase"><label for="image">icon</label></p>
        <input type="file" name="image" id="image" size="35">
        <?php if (isset($error['image']) && $error['image'] === 'type') : ?>
        <p class="error">* 画像を指定してください</p>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
        <p class="error">画像を改めて指定してください</p>
        <?php endif; ?>

        <div>
          <input class="submit-button -signup" type="submit" value="Check">
        </div>
      </form>
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