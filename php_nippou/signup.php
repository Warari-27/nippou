<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　新規登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

if(!empty($_POST)){
    
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];

    validRequired($email,'email');
    validRequired($pass,'pass');
    validRequired($pass_re,'pass_re');

    if(empty($err_msg)){
        validEmail($email,'email');
        validMaxLen($email,'email');
        validEmailDup($email);

        validHalf($pass,'pass');
        validMaxLen($pass,'pass');
        validMinLen($pass,'pass');
        if(empty($err_msg)){
            validMatch($pass,$pass_re,'pass_re');

            if(empty($err_msg)){
                try{
                    $dbh = dbConnect();
                    $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
                    $data = array(':email' => $email,':pass' => password_hash($pass,PASSWORD_DEFAULT),
                    ':login_time' =>date('Y-m-d H:i:s'),
                    ':create_date' =>date('Y-m-d H:i:s'));

                    $stmt = queryPost($dbh,$sql,$data);
                    
                    

                    if($stmt){
                        $sesLimit = 60*60;
                        $_SESSION['login_date'] = time();
                        $_SESSION['login_limit'] = $sesLimit;

                        $_SESSION['user_id'] = $dbh->lastInsertId();
                        debug('セッション変数の中身：'.print_r($_SESSION,true));
                        header("Location:index.php");
                    }
                }catch (Exception $e){
                    error_log('エラー発生：'.$e->getMessage());
                    $err_msg['common'] = MSG07;
                }
            }
        }
    }
}
?>

<?php
$siteTitle = '新規登録';
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
                メールアドレス
                <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
            </label>
            <div class="area-msg">
                <?php
                if(!empty($err_msg['email'])) echo $err_msg['email'];
                ?>
            </div>
            <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                パスワード
                <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
            </label>
            <div class="area-msg">
                <?php
                if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                ?>
            </div>
            <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                パスワード（再入力）
                <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
            </label>
            <div class="btn-container">
                <input type="submit" class="btn btn-container" value="登録">
            </div>
        </div>
    </section>
    </div>
    <?php
    require('footer.php');
    ?>
</body>