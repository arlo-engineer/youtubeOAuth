<?php
// セットアップ
require_once __DIR__ . '/vendor/autoload.php';
session_start();

// クライアントIDと秘密鍵を設定
$client_id = "999582628275-jdbma12tpptu3bja12v1o408j98qo156.apps.googleusercontent.com";
$client_secret = "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";

// リダイレクトURIを設定
$redirect_uri = "http://localhost:8888/youtubeOAuth";

// OAuth 2.0 クライアントを作成
$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setScopes(['https://www.googleapis.com/auth/youtube.readonly']);
$client->setPrompt('consent');

// アクセストークンが設定されていない場合、認証URLを生成
if (! isset($_GET['code'])) {
$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
// リロード時にアクセストークンがセッションにあればそれを復元
    if (isset($_SESSION['access_token'])) {
        $client->setAccessToken($_SESSION['access_token']);
    }
    // アクセストークンを取得->else以降が変でLaravelの方でも同様のエラーが発生していると思われる
    $auth_url = $client->createAuthUrl();
    $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $access_token = $client->getAccessToken(); // 一度目のアクセスの場合は中身が存在しているが、リロードするとNULLとなる。
    if (isset($access_token)) {
        $_SESSION['access_token'] = $access_token;
    }
    $client->setScopes(['https://www.googleapis.com/auth/youtube.readonly']);

    // YouTube Data APIにリクエストを送信
    $youtube = new Google_Service_YouTube($client);
    $response = $youtube->subscriptions->listSubscriptions('snippet,contentDetails,subscriberSnippet', array("mine" => "true"));

    // 登録チャンネル名を表示
    foreach ($response['items'] as $result) {
        $titles = $result['snippet']['title'];
        echo '<pre>';
        echo $titles;
        echo '<pre>';
    }
}
