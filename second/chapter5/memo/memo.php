<?php require('dbconnect.php'); ?>
<!doctype html>
<html lang="ja">
<head>
<!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="layout.css">
  <title>よくわかるPHPの教科書</title>
</head>
<body>
  <header>
    <h1 class="font-weight-normal">よくわかるPHPの教科書</h1>
  </header>

  <main>
    <h2>Practice</h2>
    <?php
    $id = $_GET['id'];
    if (!is_numeric($id) || $id <= 0) {
        echo '1以上の数字で指定してください';
        exit();
    }
    $memos = $db->prepare('SELECT * FROM memos WHERE id=?');
    $memos->execute(array($_GET['id']));
    $memo = $memos->fetch();
    ?>
    <article>
      <div class="pre">
        <?php echo $memo['memo']; ?>
      </div>
      <a href="index.php">戻る</a>
    </article>
  </main>
</body>
</html>