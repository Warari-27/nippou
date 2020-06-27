<header>
    <div class="site-width">
        <h1><a href="#">日報システム</a></h1>
        <nav id="top-nav">
            <ul>
            <?php
                if(empty($_SESSION['user_id'])){
            ?>
                <li><a href="signup.php">新規登録</a></li>
            <?php
                }else{
            ?>
            <li><a href="#">マイページ</a></li>
            <li><a href="#">ログアウト</a></li>
            <?php
                }
            ?>
            </ul>
        </nav>
    </div>
</header>