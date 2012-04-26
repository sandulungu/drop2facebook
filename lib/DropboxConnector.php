<?php

/**
 * Dropbox connector using PHP SDK and a custom storage engine. 
 */
class DropboxConnector {
     static function connect($storage = null) {
        if (!$storage) {
            $storage = Storage::getCurrentStorage();
        }
   
        // Set your consumer key, secret and callback URL
        $key      = '5z0r5sdd4alfq70';
        $secret   = 'bjtr9prlbw39o1k';

        // Instantiate the required Dropbox objects
        $OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, new DropboxStorage($storage), sprintf(CALLBACK_URL, 'dropbox'));
        $dropbox = new \Dropbox\API($OAuth);

        return $dropbox;
    }
    
    static function authorize() {
        self::connect();
    }
}