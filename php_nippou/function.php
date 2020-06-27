<?php
//================================
// ログ設定
//================================
ini_set('log_errors','on');
ini_set('error_log','php.log');

//================================
// デバッグ
//================================
$debug_flg = true;

function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}
//================================
// セッション
//================================
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
session_start();
session_regenerate_id();


function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面処理開始');
    debug('セッションID:'.session_id());
    debug('セッション変数の中身：'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}

//================================
// 定数（メッセージ）
//================================

define('MSG01','入力必須です');
define('MSG02','正しいメールの形式で入力してください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみ入力可能です');
define('MSG05','6文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのアカウントは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG12', '現在のパスワードが違います');
define('MSG13', '現在のパスワードと同じです');
define('MSG14', '文字で入力してください');
define('MSG15', '正しくありません');

//================================
// グローバル変数
//================================
$err_msg = array();

//================================
// バリデーションチェック
//================================
function validRequired($str,$key){
    if($str === ''){
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}
function validEmail($str,$key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}
function validEmailDup($email){
    global $err_msg;
    try{
        $dbh = dbConnect();
        $sql ='SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);

        $stmt =queryPost($dbh,$sql,$data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG08;
        }
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
function validMatch($str1,$str2,$key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}
function validMinLen($str,$key,$min = 6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}
function validMaxLen($str,$key,$max = 256){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}
function validHalf($str,$key){
    if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}

function validPass($str,$key){
    validHalf($str,$key);
    validMaxLen($str,$key);
    validMinLen($str,$key);
}
//================================
// データベース
//================================
function dbConnect(){
    $dsn = 'mysql:dbname=qa;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    $dbh = new PDO($dsn,$user,$password,$options);
    return $dbh;
}

function queryPost($dbh,$sql,$data){
    $stmt = $dbh->prepare($sql);

    if(!$stmt->execute($data)){
        debug('クエリに失敗しました');
        debug('失敗したSQL:'.print_r($stmt,true));
        $err_msg['common'] = MSG07;
        return 0;
    }
    debug('クエリ成功。');
    return $stmt;
}

function getUser($u_id){
    debug('ユーザ情報取得');
    try{
        $dbh = dbConnect();
        $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    }catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}
?>