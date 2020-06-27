<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログイン　');
debug('「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


if(!empty($POST_)){
    debug('POST送信があります。');

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    validEmail($email,'email');
    validMaxLen($email,'email');
    validHalfLen($pass,'pass');
    validMaxLen($pass,'pass');
    validMinLen($pass,'pass');

    validRequired($email,'email');
    validRequired($pass,'pass');

    if(empty($err_msg)){
        debug('バリデーションOK');

        try{
            $dbh = dbConnect();
            $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);
            $stmt = queryPost($dbh,$sql,$data);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('クエリの中身:'.print_r($result,true));

            if(!empty($result) && password_verify($password,array_shift($result))){
                debug('パスワードマッチ');

                $sesLimit = 60*60;
                $_SESSION['login_date'] = time();

                if($pass_save){
                    debug('ログイン保持にチェックあり');
                    $_SESSIOMN['login_Limit'] = $sesLimit * 24* 30;
                }else{
                    debug('ログイン保持にチェックなし');
                    $_SESSION['login_limit'] = $sesLimit;
                }
                $_SESSION['user_id'] = $result['id'];
                debug('セッション変数の中身：'.print_r($_SESSION,true));
                debug('マイページへ遷移します。');
                header("Location:index.php");
            }else{
                debug('パスワードアンマッチ');
                $err_msg['common'] = MSG07;
            }
        }catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('画面表示処理終了　<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php
$siteTitle = 'ログイン';
require('head.php');
?>

    <body>
        <?php
        require('header.php');
        ?>
        <div id="contents" class="site-width">

        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form">
                    <div class="area-msg">
                        <?php
                            if(!empty($err_msg['common'])) echo $err_msg['common'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                    メールアドレス <span>必須</span>
                    <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                    パスワード　<span>必須</span>
                    <input type="text" name="pass" value="<?php if(!empty($_POST['email'])) echo $POST['pass']; ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['pass'])) echo $err_msg['pass']; 
                        ?>
                    </div>
                    <label>
                        <input type="checkbox" name="pass_save">パスワードを保存する
                    </label>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="ログイン">
                    </div>
                    <a href="#">パスワードを忘れた方はこちらから</a>
                </form>
            </div>
        </section>
    </div>
    <?php
    require('footer.php');
    ?>
</body>
</html>