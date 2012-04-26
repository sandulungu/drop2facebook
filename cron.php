<?php

/**
 * Does all the heavy work of publishing stuff in FB.
 */

require_once 'lib/bootstrap.php';

header('Content-Type: text/plain');

$worker = new DebuggingWorker();
foreach (Storage::getStorages() as $storage) {
    $worker->run($storage);
}