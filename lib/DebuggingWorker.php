<?php

/**
 * This worker echoes it activity/debug log
 */
class DebuggingWorker extends Worker {
    
    protected function log($message) {
        echo "$message\n";
        while (ob_get_level()) {
            ob_flush();
        }
        flush();
    }
    
}