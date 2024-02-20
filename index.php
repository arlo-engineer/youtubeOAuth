<?php
// セットアップ
require_once __DIR__ . '/vendor/autoload.php';

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

// アクセストークンが設定されていない場合、認証URLを生成
if (! isset($_GET['code'])) {
$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
// アクセストークンを取得
$client->authenticate($_GET['code']);
$access_token = $client->getAccessToken();

// YouTube Data APIにリクエストを送信
$youtube = new Google_Service_YouTube($client);
$response = $youtube->subscriptions->listSubscriptions('snippet,contentDetails,subscriberSnippet', array("mine" => "true"));

// 登録チャンネル名を取得
foreach ($response['items'] as $result) {
    $titles = $result['snippet']['title'];
    echo '<pre>';
    echo $titles;
    echo '<pre>';
}

};

