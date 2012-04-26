<?php

require 'lib/facebook/src/base_facebook.php';

/**
 * Extends the BaseFacebook class with the intent of using
 * advanced storage to store user ids and access tokens.
 */
class Facebook extends BaseFacebook {

    public function getUserGroups() {
        $graph = $this->api('me/groups');
        $groups = array();
        foreach ($graph['data'] as $group) {
            $groups[$group['id']] = mb_convert_case($group['name'], MB_CASE_LOWER);
        }
        return $groups;
    }
    
    public function getUserAlbums() {
        $graph = $this->api('me/albums');
        $albums = array();
        foreach ($graph['data'] as $album) {
            $albums[$album['id']] = mb_convert_case($album['name'], MB_CASE_LOWER);
        }
        return $albums;
    }
    
    public function __construct($config, $storage) {
        $this->storage = $storage;
        parent::__construct($config);
    }

    protected function getCurrentUrl() {
        return sprintf(CALLBACK_URL, 'facebook');
    }
    
    protected static $kSupportedKeys =
            array('state', 'code', 'access_token', 'user_id');

    /**
     * Provides the implementations of the inherited abstract
     * methods.  The implementation uses PHP sessions to maintain
     * a store for authorization codes, user ids, CSRF states, and
     * access tokens.
     */
    protected function setPersistentData($key, $value) {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to setPersistentData.');
            return;
        }

        $this->storage->set($this->constructSessionVariableName($key), $value);
    }

    protected function getPersistentData($key, $default = false) {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to getPersistentData.');
            return $default;
        }

        return $this->storage->get($this->constructSessionVariableName($key), $default);
    }

    protected function clearPersistentData($key) {
        if (!in_array($key, self::$kSupportedKeys)) {
            self::errorLog('Unsupported key passed to clearPersistentData.');
            return;
        }

        return $this->storage->remove($this->constructSessionVariableName($key));
    }

    protected function clearAllPersistentData() {
        foreach (self::$kSupportedKeys as $key) {
            $this->clearPersistentData($this->constructSessionVariableName($key));
        }
    }

    protected function constructSessionVariableName($key) {
        return "facebook.$key";
    }
}
