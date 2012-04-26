<?php

/**
 * User entry point in the application. 
 */

require_once 'lib/bootstrap.php';

// Connect to a file storage provider
if (isset($_GET['provider']) && array_key_exists($_GET['provider'], $providers)) {
    $call = array(ucfirst($_GET['provider']) . 'Connector', 'connect');
    call_user_func($call);
}

// We should always be connected to FB
$facebook = FacebookConnector::connect();

// List storage providers
$items = array();
foreach ($providers as $provider => $label) {
    if (!Storage::getCurrentStorage()->get("$provider.access_token")) {
        $items[] = "<a href='?provider=$provider' class='icon $provider' title='Click to connect $label'>Connect $label</a>";
    } else {
        $items[] = "<span class='icon $provider' title='Cool, $label connected'>$label connected</span>";
    }
}
$items = implode("</li><li>", $items);
echo "<ul class='providers'><li>$items</li></ul>";

//$logout_url = $facebook->getLogoutUrl();
//echo "<a href='$logout_url'>Refresh FB</a>";

?>
<title>Drop2Facebook</title>
<style>
    .providers {
        text-align: center;
    }
    .providers li {
        display: inline-block;
        list-style: none;
    }
    span.icon {
        background-color: #ddeedd;
    }
    a.icon {
        opacity: 0.5;
        background-color: #dddddd;
    }
    a.icon:hover {
        opacity: 1;
    }
    .icon {
        border-radius: 32px;
        display: inline-block;
        width: 300px;
        height: 300px;
        text-indent: -10000px;
        background-position: center center;
        background-repeat: no-repeat;
        margin: 16px;
    }
    .facebook {
        background-image: url(images/facebook.png);
    }
    .dropbox {
        background-image: url(images/dropbox.png);
    }
    .live {
        background-image: url(images/skydrive.png);
    }
</style>