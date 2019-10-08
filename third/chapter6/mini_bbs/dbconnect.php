<?php
try {
    $db = new PDO('mysql:dbname=mydb;host=127.0.0.1;port=8889;carset=utf8', 'root', 'root');
} catch(PDOException $e) {
    print('DB接続エラー：' . $e->getMessage());
}