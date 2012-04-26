<?php

/**
 * OAuth callbacks land here. 
 */

require_once 'lib/bootstrap.php';

// Connect to a file storage provider
if (isset($_GET['provider']) && array_key_exists($_GET['provider'], $providers)) {
    $call = array(ucfirst($_GET['provider']) . 'Connector', 'authorize');
    call_user_func($call);
}

header("Location: .");
