<?php

/**
 * Facebook connector using PHP SDK and an adapted main class. 
 */
class FacebookConnector {
    static function connect($storage = null) {
        if (!$storage) {
            $storage = Storage::getCurrentStorage();
        }
        
        // Create our Application instance (replace this with your appId and secret).
        $facebook = new Facebook(array(
            'appId'  => '343081619078249',
            'secret' => '13655f1d24c87cdac3d125ef187b1f8a',
        ), $storage);

        if (!$facebook->getUser()) {
            header("Location: " . $facebook->getLoginUrl(array(
                'scope' => array('user_photos', 'user_groups', 'publish_stream', 'offline_access', 'photo_upload')
            )));
            exit;
        }

        $storage->persist($facebook->getUser());
        return $facebook;
    }
    
    static function authorize() {
        self::connect();
    }
}