<?php

/**
 * Autoloading and shared configuration. 
 */

const CALLBACK_URL = 'http://drop2facebook.local/drop2facebook/callback.php?provider=%s';
$providers = array('facebook' => 'Facebook', 'dropbox' => 'Dropbox', 'live' => 'Microsoft Live SkyDrive');

// Register a simple autoload function
spl_autoload_register(function($class){
    $class = str_replace('\\', '/', $class);
    if (strpos($class, 'Dropbox/') === 0) {
        require_once('lib/dropbox/' . $class . '.php');
    } else {
        require_once('lib/' . $class . '.php');
    }
});

session_start();
