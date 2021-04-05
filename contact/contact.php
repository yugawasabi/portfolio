<?php
//エスケープ処理やデータチェックを行う関数のファイルの読み込み
require '../libs/functions.php';

//POSTされたデータがあれば変数に格納、なければ NULL（変数の初期化）
$name = isset( $_POST[ 'name' ] ) ? $_POST[ 'name' ] : NULL;
$email = isset( $_POST[ 'email' ] ) ? $_POST[ 'email' ] : NULL;
$tel = isset( $_POST[ 'tel' ] ) ? $_POST[ 'tel' ] : NULL;
$subject = isset( $_POST[ 'subject' ] ) ? $_POST[ 'subject' ] : NULL;
$body = isset( $_POST[ 'body' ] ) ? $_POST[ 'body' ] : NULL;

//送信ボタンが押された場合の処理
if (isset($_POST['submitted'])) {

    //POSTされたデータに不正な値がないかを別途定義した checkInput() 関数で検証
    $_POST = checkInput( $_POST );

    //filter_var を使って値をフィルタリング
    if(isset($_POST['name'])) {
        //スクリプトタグがあれば除去
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['email'])) {
        //全ての改行文字を削除
        $email = str_replace(array("\r", "\n", "%0a", "%0d"), '', $_POST['email']);
        //E-mail の形式にフィルタ
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    if(isset($_POST['tel'])) {
        //数値の形式にフィルタ（数字、+ 、- 記号 以外を除去）
        $tel = filter_var($_POST['tel'], FILTER_SANITIZE_NUMBER_INT);
    }

    if(isset($_POST['subject'])) {
        $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    }

    if(isset($_POST['body'])) {
        $body = filter_var($_POST['body'], FILTER_SANITIZE_STRING);
    }

    //POST でのリクエストの場合
    if ($_SERVER['REQUEST_METHOD']==='POST') {

        //メールアドレス等を記述したファイルの読み込み
        require '../libs/mailvars.php';

        //メール本文の組み立て。値は h() でエスケープ処理
        $mail_body = 'コンタクトページからのお問い合わせ' . "\n\n";
        $mail_body .=  "お名前： " .h($name) . "\n";
        $mail_body .=  "Email： " . h($email) . "\n"  ;
        $mail_body .=  "お電話番号： " . h($tel) . "\n\n" ;
        $mail_body .=  "＜お問い合わせ内容＞" . "\n" . h($body);

        //-------- sendmail を使ったメールの送信処理 ------------

        //メールの宛先（名前<メールアドレス> の形式）。値は mailvars.php に記載
        $mailTo = mb_encode_mimeheader(MAIL_TO_NAME) ."<" . MAIL_TO. ">";

        //Return-Pathに指定するメールアドレス
        $returnMail = MAIL_RETURN_PATH; //
        //mbstringの日本語設定
        mb_language( 'ja' );
        mb_internal_encoding( 'UTF-8' );

        // 送信者情報（From ヘッダー）の設定
        $header = "From: " . mb_encode_mimeheader($name) ."<" . $email. ">\n";
        $header .= "Cc: " . mb_encode_mimeheader(MAIL_CC_NAME) ."<" . MAIL_CC.">\n";
        $header .= "Bcc: <" . MAIL_BCC.">";

        //メールの送信結果を変数に代入 （サンプルなのでコメントアウト）
        if ( ini_get( 'safe_mode' ) ) {
            //セーフモードがOnの場合は第5引数が使えない
            $result = mb_send_mail( $mailTo, $subject, $mail_body, $header );
        } else {
            $result = mb_send_mail( $mailTo, $subject, $mail_body, $header, '-f' . $returnMail );
        }

        //メールが送信された場合の処理
        if ( $result ) {
            //空の配列を代入し、すべてのPOST変数を消去
            $_POST = array();

            //変数の値も初期化
            $name = '';
            $email = '';
            $tel = '';
            $subject = '';
            $body = '';

            //再読み込みによる二重送信の防止
            $params = '?result='. $result;
            $url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
            header('Location:' . $url . $params);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>コンタクトフォーム</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="contact/contact-form.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Shippori+Mincho+B1:wght@500&display=swap" rel="stylesheet">
</head>
<body>
<div class="container main">
    <h2 class="">お問い合わせフォーム</h2>
    <?php  if ( isset($_GET['result']) && $_GET['result'] ) : // 送信が成功した場合?>
        <h4>ありがとうございます。</h4>
        <p>送信完了いたしました。</p>
        <hr>
    <?php elseif (isset($result) && !$result ): // 送信が失敗した場合? ?>
        <h4>送信失敗</h4>
        <p>申し訳ございませんが、送信に失敗しました。</p>
        <p>しばらくしてもう一度お試しになるか、メールにてご連絡ください。</p>
        <p>メール：<a href="mailto:info@example.com">Contact</a></p>
        <hr>
    <?php endif; ?>
    <p>以下のフォームからお問い合わせください。</p>
    <form id="form" method="post">
        <div class="form-group">
            <label for="name">お名前（必須）</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="氏名" required value="<?php echo h($name); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email（必須）</label>
            <input  type="email" class="form-control" id="email" name="email" placeholder="Email アドレス" required value="<?php echo h($email); ?>">
        </div>
        <div class="form-group">
            <label for="tel">お電話番号（半角英数字）</label>
            <input type="tel" class="form-control" id="tel" name="tel" value="<?php echo h($tel); ?>" placeholder="お電話番号（半角英数字でご入力ください）">
        </div>
        <div class="form-group">
            <label for="subject">件名（必須）</label>
            <input type="text" class="form-control" id="subject" name="subject" placeholder="件名" required value="<?php echo h($subject); ?>">
        </div>
        <div class="form-group">
            <label for="body">お問い合わせ内容（必須）</label>
            <textarea class="form-control" id="body" name="body" placeholder="お問い合わせ内容（1000文字まで）をお書きください" required rows="3"><?php echo h($body); ?></textarea>
        </div>
        <button name="submitted" type="submit" class="btn btn-primary">送信</button>
    </form>
</div>
</body>
</html>