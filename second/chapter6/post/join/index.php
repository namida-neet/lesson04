<?php
session_start();

if (!empty($_POST)) {
    // エラー項目の確認
    if ($_POST['name']  === '') {
        $error['name'] = 'blank';
    }
    if ($_POST['email'] === '') {
        $error['email'] = 'blank';
    }
    if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    if ($_POST['password'] === '') {
        $error['password'] = 'blank';
    }
    $fileName = $_FILES['image']['name'];
    if (!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext !== 'jpg' && $ext !== 'gif' && $ext !== 'png') {
            $error['image'] = 'type';
        }
    }

    if (empty($error)) {
        // 画像をアップロードする
        $image = date('YmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
    }

    if (empty($error)) {
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        // header('Location: check.php');
        // exit();
    }

  // 確認用
  if (isset($image)) {
    echo '↓$image';
    var_dump($image);
  }

  echo '↓$_FILES';
  var_dump($_FILES);

  if (isset($error)) {
    echo '↓$error';
    var_dump($error);
  }

  echo '↓$_POST';
  var_dump($_POST);

  echo '↓$_SESSION';
  var_dump($_SESSION);
}
?>
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
    <div id="content">
      <form action="" method="post" enctype="multipart/form-data">
        <dl>
          <dt>
            ニックネーム<span class="required">必須</span>
          </dt>
          <dd>
            <input type="text" name="name" size="35" maxlength="255" value="<?php if (isset($_POST['name'])) {echo htmlspecialchars($_POST['name'], ENT_QUOTES); }  ?>">
            <?php if (isset($error['name'])) { if ($error['name'] === 'blank') : ?>
            <p class="error">* ニックネームを入力してください</p>
            <?php endif; } ?>
          </dd>
          <dt>
            メールアドレス<span class="required">必須</span>
          </dt>
          <dd>
            <input type="text" name="email" size="35" maxlength="255" value=<?php if (isset($_POST['email'])) { echo htmlspecialchars($_POST['email'], ENT_QUOTES); } ?>>
            <?php if (isset($error['email'])) { if ($error['email'] === 'blank') : ?>
            <p class="error">* メールアドレスを入力してください</p>
            <?php endif; } ?>
          </dd>
          <dt>
            パスワード<span class="required">必須：4文字以上</span>
          </dt>
          <dd>
            <input type="password" name="password" size="10" maxlength="20" value="<?php if (isset($_POST['password'])) { echo htmlspecialchars($_POST['password'], ENT_QUOTES); } ?>">
            <?php if (isset($error['password'])) { if ($error['password'] === 'blank' || $error['password'] === 'length') : ?>
            <p class="error">* パスワードを4文字以上で入力してください</p>
            <?php endif; } ?>
          </dd>
          <dt>
            写真など<span class="required">「.gif」「.jpg」「.png」の画像を指定してください</span>
          </dt>
          <dd>
            <input type="file" name="image" size="35">
            <?php if (isset($error['image'])) {if ($error['image'] === 'type') : ?>
            <p class="error">拡張子が違います</p>
            <?php endif; } ?>
            <?php if (!empty($error)) : ?>
            <p class="error">画像を改めて指定してください</p>
            <?php endif; ?>
          </dd>
        </dl>
        <div>
          <input type="submit" value="入力内容を確認する">
        </div>
      </form>
    </div>

  </div>
</body>

</html>