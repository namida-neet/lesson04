<?php
try {
    $db = new PDO(
        'mysql:dbname=mini_bbs;host=127.0.0.1;port=8889;charset=utf8',
        'root',
        'root',
        array(
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_EMULATE_PREPARES => false,
        )
    );
} catch (PDOException $e) {
    echo 'DB接続エラー：　' . $e->getMessage();
}
