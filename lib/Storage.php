<?php

/**
 * File storage engine.
 * 
 * Automatically syncs with session.
 * Uses FB user id as primary key.
 */
class Storage {
    
    static private $instances, $allData;
    
    private $id, $data;
    
    const FILENAME = 'lib/data.txt';
    
    /**
     * @return Storage
     */
    static public function getCurrentStorage() {
        $id = isset($_SESSION['id']) ? $_SESSION['id'] : '__session';
        $storage = self::getStorage($id);
        if (empty($storage)) {
            $storage = self::$instances[$id] = new self();
            if ($id != '__session') {
                $storage->id = $_SESSION['id'];
            } else {
                $storage->data =& $_SESSION['data'];
            }
        }
        return $storage;
    }
    
    static private function loadStorages() {
        if (!isset(self::$instances)) {
            self::$instances = array();
            self::$allData = unserialize(file_get_contents(self::FILENAME));
        }
    }
    
    static public function getStorages() {
        self::loadStorages();
        if (!self::$allData) {
            return array();
        }
        foreach (self::$allData as $id => $data) {
            if (!self::getStorage($id)) {
                self::$instances[$id] = $storage = new self();
                $storage->id = $id;
            }
        }
        return self::$instances;
    }
    
    static public function getStorage($id) {
        self::loadStorages();
        return @self::$instances[$id];
    }
    
    static private function flush() {
        file_put_contents(self::FILENAME, serialize(self::$allData));
    }
    
    public function persist($id) {
        if ($this->id == $id) {
            return $this;
        }
        self::$allData[$id] = (array)$this->data + @(array)self::$allData[$id];
        if ($this->id) {
            unset(self::$instances[$this->id]);
        } else {
            unset($_SESSION['data']);
        }
        self::$instances[$id] = $this;
        $this->id = $id;
        $_SESSION['id'] = $id;
        return $this;
    }
    
    public function all() {
        return $this->id ? @self::$allData[$this->id] : $this->data;
    }
    
    public function get($key, $default = null) {
        $value = $this->id ? @self::$allData[$this->id][$key] : @$this->data[$key];
        return $value ? $value : $default;
    }
    
    public function set($key, $value) {
        if ($this->id) {
            self::$allData[$this->id][$key] = $value;
            self::flush();
        } else {
            $this->data[$key] = $value;            
        }
        return $this;
    }

    public function remove($key) {
        if ($this->id) {
            unset(self::$allData[$this->id][$key]);
            self::flush();
        } else {
            unset($this->data[$key]);            
        }
        return $this;
    }
}
