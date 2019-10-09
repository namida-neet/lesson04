<?php
session_start();
require('../dbconnect.php');

function h($str) {
  echo htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

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
  <link rel="stylesheet" href="../style.css" />
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1 class="uppercase">Sign up</h1>
      <p class="head__btn"><a href="../login.php"">Login</a></p>
    </div><!-- #head -->

    <div id="content">
      <form action="" method="post" enctype="multipart/form-data">
        <dl>
          <dt class="uppercase">name<span class="required">mandatory field</span></dt>
          <dd>
            <input type="text" name="name" size="35" maxlength="255" value="<?php if (isset($_POST['name'])) {h($_POST['name']);} ?>" />
          <?php if (isset($error['name'])) : ?>
            <?php if ($error['name'] === 'blank') : ?>
              <p class="error">* ニックネームを入力してください</p>
            <?php endif; ?>
          <?php endif; ?>
          </dd>
          <dt class="uppercase">email address<span class="required">mandatory field</span></dt>
          <dd>
            <input type="text" name="email" size="35" maxlength="255" value="<?php if (isset($_POST['email'])) {h($_POST['email']);} ?>" />
          <?php if (isset($error['email'])) : ?>
            <?php if ($error['email'] === 'blank') : ?>
              <p class="error">* メールアドレスを入力してください</p>
            <?php endif; ?>
            <?php if ($error['email'] === 'duplicate') : ?>
              <p class="error">* このメールアドレスはすでに登録されています</p>
            <?php endif; ?>
          <?php endif; ?>
          <dt class="uppercase">password<span class="required">mandatory field</span></dt>
          <dd>
            <input type="password" name="password" size="10" maxlength="20" value="<?php if (isset($_POST['password'])) {h($_POST['password']);} ?>" />
          <?php if (isset($error['password'])) : ?>
            <?php if ($error['password'] === 'blank') : ?>
              <p class="error">* パスワードを入力してください</p>
            <?php endif; ?>
            <?php if ($error['password'] === 'length') : ?>
              <p class="error">* パスワードは4文字以上で入力してください</p>
            <?php endif; ?>
          <?php endif; ?>
          </dd>
          <dt class="uppercase">icon</dt>
          <dd>
            <div id="icon-upload-btn">upload</div>
            <input id="icon-picture" type="file" name="image" size="35" value="test" />
            <input type="text" id="filename" placeholder="" readonly />
            <?php if (isset($error['image'])) : ?>
              <?php if ($error['image'] === 'type') : ?>
                <p class="error">* 画像を指定してください</p>
              <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($error)) : ?>
              <p class="error">画像を改めて指定してください</p>
            <?php endif; ?>
          </dd>
        </dl>
        <div><input class="submit-button" type="submit" value="Check" /></div>
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
  <script type="text/javascript">
  $(function() {
      $('#icon-picture').css({
          'position': 'absolute',
          'top': '-9999px'
      }).change(function() {
          var val = $(this).val();
          var path = val.replace(/\\/g, '/');
          var match = path.lastIndexOf('/');
      $('#filename').css("display","inline-block");
          $('#filename').val(match !== -1 ? val.substring(match + 1) : val);
      });
      $('#filename').bind('keyup, keydown, keypress', function() {
          return false;
      });
      $('#filename, #icon-upload-btn').click(function() {
          $('#icon-picture').trigger('click');
      });
  });
  </script>
</body>

</html>