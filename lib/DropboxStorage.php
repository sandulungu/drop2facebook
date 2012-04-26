<?php

/**
 * Dropbox storage adapter
 */
class DropboxStorage implements \Dropbox\OAuth\Storage\StorageInterface {
    
    function __construct($storage) {
        $this->storage = $storage;
    }
    
    public function get($type) {
        return $this->storage->get("dropbox.$type");
    }
    
    public function set($token, $type) {
        $this->storage->set("dropbox.$type", $token);
    }
}
