<?php
try {
    $db= new PDO('mysql:dbname=mini_bbs;host=127.0.0.1;port=8889;carset=utf8', 'root', 'root');
    echo '接続OK';
} catch (PDOException $e) {
    echo '接続エラー' . $e->getMessage();
}
if (isset($db)) {
  var_dump($db);
}
if (isset($e)) {
  var_dump($e);
}
?>