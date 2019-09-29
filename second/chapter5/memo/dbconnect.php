<?php
try {
   $db = new PDO('mysql:dbname=mydb;host=127.0.0.1;port=8889;charset=utf8', 'root', 'root');
   echo '接続OK';
} catch (PDOException $e) {
    echo '接続エラー：' . $e->getMessage();
}
?>