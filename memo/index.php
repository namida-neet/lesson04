<?php require('dbconnect.php'); ?>
<!doctype html>
<html lang="ja">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/style.css">

    <title>index.php</title>
</head>

<body>
    <header>
        <h1 class="font-weight-normal">よくわかるPHPの教科書</h1>
    </header>

    <main>
        <h2>Practice</h2>
        <?php
        //$memos = $db->query('SELECT * FROM memos ORDER BY id LIMIT 0,5');
        $memos = $db->prepare('SELECT * FROM memos ORDER BY id LIMIT ?,5');
        $memos->bindParam(1, $_REQUEST['page'], PDO::PARAM_INT);
        $memos->execute();
        ?>
        <article>
            <?php while ($memo = $memos->fetch()) : ?>
                <p>
                    <a href="memo.php?id=<?php print($memo['id']); ?>">
                        <?php print(mb_substr($memo['memo'], 0, 50)); ?>
                        <?php print((mb_strlen($memo['memo'])) > 50 ? '...' : ''); ?>
                    </a>
                </p>
                <time><?php print($memo['created_at']); ?></time>
                <hr>
            <?php endwhile; ?>
        </article>
    </main>
</body>

</html>