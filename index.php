<?php

require_once __DIR__ . '/vendor/autoload.php';


if(!session_id()) {
    session_start();
}


class FbMessenger{
    protected $fb;
    protected $helper;
    protected $permissions;
    protected $callback;
    protected $loginUrl;
    protected $accessToken;

    public function __construct(){
        $this->fb = new Facebook\Facebook([
            'app_id' => '435318296547275',
            'app_secret' => '061afcf6ba7a56499d6034a6e9ce53ca',
            'cookie' => true
        ]);
        $this->helper = $this->fb->getRedirectLoginHelper();
        $this->permissions = ['manage_pages', 'publish_pages', 'pages_messaging', 'read_page_mailboxes', 'pages_messaging_phone_number'];
        $this->callback = 'http://localhost:8000/fb-callback.php';
        $this->loginUrl = $this->helper->getLoginUrl($this->callback, $this->permissions);
        $this->accessToken = $this->helper->getAccessToken();
        //get user accounts    

        if (isset($_SESSION['facebook_access_token'])){
            $this->accessToken  = $_SESSION['facebook_access_token']; 
            $this->fb->setDefaultAccessToken($this->accessToken);
        }else {
            echo '<a href="'.$this->loginUrl.'">Log in with Facebook!</a>';
            exit;
        }

    }

    private function getPageAccesToken($page){
        $requestParam = $page.'?fields=access_token';
        $request = $this->fb->request('GET', $requestParam);
        $response = $this->fb->getClient()->sendRequest($request);
        $object = $response->getGraphObject();
        return $object->getField('access_token');
    }

    public function getPageConversations($page){
        $pageAccessToken = $this->getPageAccesToken($page);

        $requestParam = '/'.$page.'/conversations';
        $response = $this->fb->get($requestParam, $pageAccessToken);
        return $response->getDecodedBody();
    }
    
    public function getPageConversationMessages($page, $conversationId){
        $pageAccessToken = $this->getPageAccesToken($page);

        $requestParam = '/'.$conversationId.'/messages?fields=message,from,id,subject,to,attachments,shares,tags,created_time';
        $response = $this->fb->get($requestParam, $pageAccessToken);
        $messages = $response->getDecodedBody();
        return $messages;
    }
    
}


$fbMessenger= new FbMessenger;

if(isset($_SESSION['facebook_access_token'])){
    echo 'session: '.$_SESSION['facebook_access_token'];
    echo "<hr>";
    echo "<pre>";
    var_dump($fbMessenger->getPageConversations('vlogers'));
    echo "<hr>";
    $messages = $fbMessenger->getPageConversationMessages('vlogers', 't_mid.1467627047595:4e7ca8368bb2fa4581');
    foreach($messages['data'] as $key => $value){
        echo "id: ".$value['id']."<br>";
        echo "from: ".$value['from']['name']."<br>";
        echo "message: ".$value['message']."<br>";
        echo "<hr>";
    }
}
