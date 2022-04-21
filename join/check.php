<?php
session_start();
require('../library.php');

if (isset($_SESSION['form'])) {
    $form = $_SESSION['form'];
} else {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = dbconnect();
    $stmt = $db->prepare('insert into members (name, email, password, height, weight, target_weight) values (?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        die($db->error);
    }
    $password = password_hash($form['password'], PASSWORD_DEFAULT);
    $stmt->bind_param('sssiii', $form['name'], $form['email'], $password, $form['height'], $form['weight'], $form['target_weight']);
    $success = $stmt->execute();
    if (!$success) {
        $die($db->error);
    }
    
    unset($_SESSION['form']);
    header('Location: thanks.php');
}


?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>会員登録画面</title>

    <link rel="stylesheet" href="../style.css" />
</head>
<body>
    <div id="wrap">
		<div id="head">
			<h1>会員登録</h1>
		</div>

		<div id="content">
			<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
			<form action="" method="post">
				<dl>
					<dt>ニックネーム</dt>
					<dd><?php echo h($form['name']); ?></dd>
					<dt>メールアドレス</dt>
					<dd><?php echo h($form['email']); ?></dd>
					<dt>パスワード</dt>
					<dd>
						【表示されません】
					</dd>
					<dt>身長</dt>
					<dd><?php echo h($form['height']); ?></dd>
                    <dt>現在の体重</dt>
                    <dd><?php echo h($form['weight']); ?></dd>
                    <dt>目標体重</dt>
                    <dd><?php echo h($form['target_weight']); ?></dd>
				</dl>
				<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
			</form>
		</div>

	</div>
</body>
</html>