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
    $statement = $db->prepare('INSERT INTO memos SET memo=?, created_at=NOW()');
    $statement->bindParam(1, $_POST['memo'], );
    $statement->execute();
    var_dump($_POST['memo']);
    echo 'メモが登録されました';
    ?>
  </main>
</body>
</html>