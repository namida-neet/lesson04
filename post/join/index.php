<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="../style.css" />
</head>

<body>
  <div id="wrap">
    <div id="head">
      <h1>会員登録</h1>
    </div>

    <?php
    session_start();

    if (!empty($_POST)) {
      //エラー項目の確認
        if ($_POST['name'] == '') {
            $error['name'] = 'blank';
        }
        if ($_POST['email'] == '') {
            $error['email'] = 'blank';
        }
        if (strlen($_POST['password']) < 4) {
            $error ['password'] = 'length';
        }
        if ($_POST['password'] == '') {
            $error['password'] = 'blank';
        }

        if (empty($error)) {
            $_SESSION['join'] = $_POST;
            header('Location: chack.php');
            exit();
        }
    }
    ?>

    <div id="content">
      <p>次のフォームに必要事項をご記入ください。</p>
      <form action="" method="post" enctyoe="multipart/form-data">
        <dl>
          <dt>ニックネーム<span class="required">必須</span></dt>
          <dd>
            <input type="text" name="name" size="35" maxlength="255" value="<?php if (isset($_POST['name'])) { echo htmlspecialchars($_POST['name'], ENT_QUOTES); } ?>">
            <?php if (isset($error['name']) && $error['name'] == 'blank') : ?>
            <p class="error">* ニックネームを入力してください</p>
            <?php endif; ?>
          </dd>
          <dt>メールアドレス<span class="required">必須</span></dt>
          <dd>
            <input type="text" name="email" size="35" maxlength="255" value="<?php if (isset($_POST['email'])) { echo htmlspecialchars($_POST['email'], ENT_QUOTES); } ?>">
            <?php if (isset($error['email']) && $error['email'] == 'blank') : ?>
            <p class="error">* メールアドレスを入力してください</p>
            <?php endif; ?>
          </dd>
          <dt>パスワード<span class="required">必須</span></dt>
          <dd>
            <input type="password" name="password" size="10" maxlength="20" value="<?php if (isset($_POST['password'])) { echo htmlspecialchars($_POST['password'], ENT_QUOTES); } ?>">
            <?php if (isset($error['password']) && $error['password'] == 'blank') : ?>
            <p class="error">* パスワードを入力してください</p>
            <?php endif; ?>
            <?php if (isset($error['password']) && $error['password'] == 'length') : ?>
            <p class="error">* パスワードは4文字以上で入力してください</p>
            <?php endif; ?>
          </dd>
          <dt>写真など</dt>
          <dd><input type="file" name="image" size="35"></dd>
        </dl>
        <br>
        <div><input type="submit" value="入力内容を確認する"></div>
      </form>

    </div>

  </div>
</body>

</html>