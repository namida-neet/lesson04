<?php require('dbconnect.php'); ?>
<!doctype html>
<html lang="ja">
<head>
<!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="style.css">
  <title>よくわかるPHPの教科書</title>
</head>
<body>
  <header>
    <h1 class="font-weight-normal">よくわかるPHPの教科書</h1>
  </header>

  <main>
    <h2>Practice</h2>
    <?php
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        echo '↓$id($_GET[id])';
        var_dump($id);
        $statement = $db->prepare('DELETE FROM memos WHERE id=?');
        $statement->execute(array($id));
        echo '↓array($id)';
        var_dump(array($id));
    }
    ?>
    <div class="pre">
      <p>メモを削除しました</p>
    </div>
    <p><a href="index.php">戻る</a></p>
  </main>
</body>
</html>