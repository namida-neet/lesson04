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
        // echo '↓$id';
        // var_dump($id);
        $memos = $db->prepare('SELECT * FROM memos WHERE id=?');
        $memos->execute(array($id));
        $memo = $memos->fetch();
        // echo '↓$memos';
        // var_dump($memos);
        // echo '↓$memo';
        // var_dump($memo);
    }
    ?>
    <form action="update_do.php" method="post">
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <textarea name="memo" id="" cols="50" rows="10"><?php echo $memo['memo']; ?></textarea><br>
      <button type="submit">登録する</button>
    </form>
  </main>
</body>
</html>