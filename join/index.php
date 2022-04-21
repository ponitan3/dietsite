<?php
session_start();

// ライブラリの読み取り
require('../library.php');
// 書き換え　or　項目の初期値
if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    $form = [
        'name' => '',
        'email' => '',
        'password' => '',
        'heiget' => '',
        'weight' => '',
        'target_weight' => ''
    ];
}

$error = [];

// フォームの内容チェック
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 名前のチェック
    $form['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    if($form['name'] === '') {
        $error['name'] = 'blank';
    }
echo $form['name'];
    // emailのチェック
    $form['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    if($form['email'] === '') {
        $error['email'] = 'blank';
    } else {
        $db = dbconnect();
        $stmt = $db->prepare('select count(*) from members where email=?');
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bind_param('s', $form['email']);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }

        $stmt->bind_result($cnt);
        $stmt->fetch();

        if ($cnt > 0) {
            $error['email'] = 'duplicate';
        }

    }

    // passwordのチェック
    $form['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    if ($form['password'] === '') {
        $error['password'] = 'blank';
    } else if(strlen($form['password']) < 4) {
        $error['password'] = 'length';
    }

echo filter_input(INPUT_POST, 'height', FILTER_SANITIZE_NUMBER_FLOAT);
    // 身長のチェック
    $form['height'] = filter_input(INPUT_POST, 'height');
    if ($form['height'] === '') {
        $error['height'] = 'blank';
    } else if($form['height'] < 50 || $form['height'] > 300) {
        $error['height'] = 'mistake';
    }
    echo $form['height'];
    // 現在の体重チェック
    $form['weight'] = filter_input(INPUT_POST, 'weight');
    if ($form['weight'] === '') {
        $error['weight'] = 'blank';
    } else if ($form['weight'] < 10 || $form['weight'] > 500) {
        $error['weight'] = 'mistake';
    }

    // 目標体重のチェック
    $form['target_weight'] = filter_input(INPUT_POST, 'target_weight');
    if ($form['target_weight'] === '') {
        $error['target_weight'] = 'blank';
    } else if ($form['target_weight'] < 10 || $form['weight'] > 500) {
        $error['target_weight'] = 'mistake';
    }

    if (empty($error)) {
        $_SESSION['form'] = $form;

        header('Location: check.php');
        exit;
    }

}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録</title>

    <link rel="stylesheet" href="../style.css"/>
</head>
<body>
<div id="wrap">
    <div id="head"> 
        <h1>会員登録</h1>
    </div>

    <div id="content">
        <p>次のフォームに必要事項をご記入ください。</p>
        <form action="" method="post">
            <dl>
                <dt>ニックネーム<span class="required">必須</span></dt>
                <dd>
                    <input type="text" name="name" size="35" maxlength="255" value="<?php echo h($form['name']); ?>"/>
                    <?php if(isset($error['name']) && $error['name'] === 'blank'): ?>
                        <p class="error">* ニックネームを入力してください</p>
                    <?php endif; ?>
                </dd>
                <dt>メールアドレス<span class="required">必須</span></dt>
                <dd>
                    <input type="text" name="email" size="35" maxlength="255" value="<?php echo h($form['email']); ?>"/>
                    <?php if(isset($error['email']) && $error['email'] === 'blank'): ?>
                        <p class="error">* メールアドレスを入力してください</p>
                    <?php endif; ?>
                    <?php if(isset($error['email']) && $error['email'] === 'duplicate'): ?>
                        <p class="error">* 指定されたメールアドレスはすでに登録されています</p>
                    <?php endif; ?>
                <dt>パスワード<span class="required">必須</span></dt>
                <dd>
                    <input type="password" name="password" size="10" maxlength="20" value=""/>
                    <?php if(isset($error['password']) && $error['password'] === 'blank'): ?> 
                        <p class="error">* パスワードを入力してください</p>
                    <?php endif; ?>
                    <?php if(isset($error['password']) && $error['password'] === 'length'): ?>
                        <p class="error">* パスワードは4文字以上で入力してください</p>
                    <?php endif; ?>
                </dd>
                <dt>身長(cm)<span class="required">必須</span></dt>
                <dd>
                    <input type="number" step="0.1" name="height" size="10" maxlength="20" value="<?php echo h($form['height']); ?>"/>
                    <?php if(isset($error['height']) && $error['height'] === 'blank'): ?>
                        <p class="error">* 身長を入力してください</p>
                    <?php endif; ?>
                    <?php if(isset($error['height']) && $error['height'] === 'mistake'): ?>
                        <p class="error">* 身長が正しくありません</p>
                    <?php endif; ?>
                </dd>
                <dt>現在の体重(kg)<span class="required">必須</span></dt>
                <dd>
                    <input type="number" step="0.1" name="weight" size="10" maxlength="20" value="<?php echo h($form['weight']); ?>"/>
                    <?php if(isset($error['weight']) && $error['weight'] === 'blank'): ?>
                        <p class="error">* 現在の体重を入力してください</p>
                    <?php endif; ?>
                    <?php if(isset($error['weight']) && $error['weight'] === 'mistake'): ?>
                        <p class="error">* 現在の体重が正しくありません</p>
                    <?php endif; ?>
                </dd>
                <dt>目標体重(kg)<span class="required">必須</span></dt>
                <dd>
                    <input type="number" step="0.1" name="target_weight" size="10" maxlength="20" value="<?php echo h($form['target_weight']); ?>"/>
                    <?php if(isset($error['target_weight']) && $error['target_weight'] === 'blank'): ?>
                        <p class="error">* 目標体重を入力してください</p>
                    <?php endif; ?>
                    <?php if(isset($error['target_weight']) && $error['target_weight'] === 'mistake'): ?>
                        <p class="error">* 目標体重が正しくありません</p>
                    <?php endif; ?>
                </dd>
            </dl>
            <div><input type="submit" value="内容を確認"/></div>
        </form>
    </div>
</div>
</body>
</html>