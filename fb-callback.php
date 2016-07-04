<?php

require_once __DIR__ . '/vendor/autoload.php';

echo '<a href="http://localhost:8000">index</a><br><br>';

if(!session_id()) {
    session_start();
}


$fb = new Facebook\Facebook([
  'app_id' => '435318296547275',
  'app_secret' => '061afcf6ba7a56499d6034a6e9ce53ca',
  'default_graph_version' => 'v2.6',
]);

$helper = $fb->getRedirectLoginHelper();
try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // There was an error communicating with Graph
  echo $e->getMessage();
  exit;
}
if (isset($accessToken)) {
    $client = $fb->getOAuth2Client();

    try {
      $accessToken = $client->getLongLivedAccessToken($accessToken);
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      echo $e->getMessage();
      exit;
    }


  $_SESSION['facebook_access_token'] = (string) $accessToken;
  echo $_SESSION['facebook_access_token'];
  exit;
} elseif ($helper->getError()) {
  // The user denied the request
  // You could log this data . . .
  var_dump($helper->getError());
  var_dump($helper->getErrorCode());
  var_dump($helper->getErrorReason());
  var_dump($helper->getErrorDescription());
  // You could display a message to the user
  // being all like, "What? You don't like me?"
  exit;
}

// If they've gotten this far, they shouldn't be here
http_response_code(400);
exit;