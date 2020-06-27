<?php
if(!empty($_SESSION['login_date'])){
    debug('ログイン済みです');

    if(($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
        debug('ログインの有効期限がオーバー');

        session_destroy();
        header("Location:login.php");
    }else{
        debug('ログイン有効期限以内');
        $_SESSION['login_date'] = time();

        if(basename($_SERVER['PHP_SELF']) ==='login.php'){
            debug('マイページへ遷移');
            header("Location:mypage.php");
        }
    }
}else{
    debug('未ログインユーザ');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
        header("Location:login.php");
    }
}
?>