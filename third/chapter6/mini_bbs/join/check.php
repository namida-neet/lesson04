<?php
session_start();
require('../dbconnect.php');
require('../htmlspecialchars.php');

if (!isset($_SESSION['join'])) {
  header('Location: index.php');
  exit();
}

if (!empty($_POST)) {
  $statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, picture=?, created=NOW()');
  $statement->execute(array(
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
  <title>Check</title>

  <link rel="stylesheet" href="../style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1 class="uppercase">Check</h1>
      <p class="header-button"><a href="../login.php"">Login</a></p>
    </div>

    <div id="content">
      <form action="" method="post">
        <input type="hidden" name="action" value="submit" />
        <dl>
          <dt class="uppercase">name</dt>
          <dd>
            <?php h($_SESSION['join']['name']); ?>
          </dd>
          <dt class="uppercase">email address</dt>
          <dd>
            <?php h($_SESSION['join']['email']); ?>
          </dd>
          <dt class="uppercase">password</dt>
          <dd>
            ********
          </dd>
          <dt class="uppercase">icon</dt>
          <dd>
            <?php if ($_SESSION['join']['image'] !== '') : ?>
            <img src="../member_picture/<?php h($_SESSION['join']['image']); ?>" alt="<?php h($_SESSION['join']['name']); ?>のアイコン画像">
            <?php endif; ?>
          </dd>
        </dl>
        <div>
          <input class="submit-button -ok" type="submit" value="OK" />
          <a class="cancel-button" href="index.php?action=rewrite">Return</a>
        </div>
      </form>
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