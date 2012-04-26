<?php

/**
 * Simple class for API calls.
 */
class LiveProvider {
    
    function __construct($access_token) {
        $this->access_token = $access_token;
    }
    
    function get($url) {
        return json_decode(file_get_contents("https://apis.live.net/v5.0/$url?access_token={$this->access_token}"));
    }
}
