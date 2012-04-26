<?php

/**
 * REST adapter for Microsoft Live! services.
 * 
 * This is a really ugly implementation, but it works.
 */
class LiveConnector {
    static function connect($storage = null) {
        if (!$storage) {
            $storage = Storage::getCurrentStorage();
        }
        
        if (!$storage->get("live.access_token")) {
            $url = urlencode(sprintf(CALLBACK_URL, 'live'));
            $cid = '00000000440BC350';
            $scope = urlencode(implode(' ', array('wl.signin', 'wl.basic')));
            header("Location: https://login.live.com/oauth20_authorize.srf?client_id=$cid&scope=$scope&response_type=token&redirect_uri=$url");
            exit;
        }
        
        return new LiveProvider(Storage::getCurrentStorage()->get("live.access_token"));
    }
    
    static function authorize() {
        if (!isset($_GET['access_token'])) {
            // quick-and-dirty hack for sending acces token to the server
            echo '<script>location.href = (""+location).replace("#", "&");</script>';
            exit;
        }

        Storage::getCurrentStorage()->set("live.access_token", $_GET['access_token']);
    }
}