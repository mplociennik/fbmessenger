<?php


require_once __DIR__ . '/vendor/autoload.php';


if(!session_id()) {
    session_start();
}


class FbMessenger{
    protected $fb;
    protected $helper;

    public function __construct(){
        $this->fb = new Facebook\Facebook([
            'app_id' => '435318296547275',
            'app_secret' => '061afcf6ba7a56499d6034a6e9ce53ca',
        ]);
        
        $this->helper = $this->fb->getRedirectLoginHelper();

    }

    

    private function getMessage($messageId){
        $requestParam = '/'.$messageId;
        $request = new FacebookRequest($session, 'GET', $requestParam);
        $response = $request->execute();
        $graphObject = $response->getGraphObject();

        return $graphObject;
    }



    
    $permissions = ['email', 'user_posts', 'pages_messaging', 'manage_pages', 'pages_messaging_phone_number', 'read_page_mailboxes'];
    $callback = 'http://localhost:8000/fb-callback.php';
    $loginUrl = $helper->getLoginUrl($callback, $permissions);


    if(!$_SESSION['facebook_access_token']){
        echo '<a href="'.$loginUrl.'">Log in with Facebook!</a>';
    }else{
        echo  $_SESSION['facebook_access_token'];
    }
}

$init = new FbMessenger;