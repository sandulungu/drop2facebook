<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Worker {
    
    protected function log($message) {
    }
    
    public function run($storage) {
        if (!$storage->get("facebook.access_token")) {
            die('Facebook not connected.');
        }
        $this->log("Connecting to FB... ");
        $facebook = FacebookConnector::connect($storage);
        $this->log("  Authorized as @" . $facebook->getUser());
        $this->log("  Retrieving groups...");
        $groups = $facebook->getUserGroups();
        $this->log("  Retrieving albums...");
        $albums = $facebook->getUserAlbums();

        if ($storage->get("live.access_token")) {
            $this->log("\nScanning SkyDrive...");
        }

        $items = array();
        if ($storage->get("dropbox.access_token")) {
            $this->log("\nScanning Dropbox...");

            $dropbox = DropboxConnector::connect($storage);
            $delta = $dropbox->delta($storage->get('dropbox.delta'));
            $storage->set('dropbox.delta', $delta['body']->cursor);
            $count = count($delta['body']->entries);
            $this->log("  $count new items found (delta: {$delta['body']->cursor})");

            $items = array();
            foreach ($delta['body']->entries as $entry) {
                if (!empty($entry[1])) { // A new file has been uploaded
                    if (!$entry[1]->is_dir) {
                        $item = array(
                            'name' => ltrim($entry[0], '/')
                        );
                        $parts = explode('/', $entry[0], 3);

                        // Do we post to a group?
                        if (count($parts) == 3) {
                            if (in_array($parts[1], $groups)) {
                                $item['group'] = array_search($parts[1], $groups);
                                $item['name'] = $parts[2];
                            }
                        }

                        // Do we have an image here?
                        if (empty($item['group']) && strpos($entry[1]->mime_type, 'image/') === 0) {
                            if (count($parts) == 3) {
                                if (in_array($parts[1], $albums)) {
                                    $item['album'] = array_search($parts[1], $albums);
                                } else {

                                    // Create new album
                                    $this->log("  Creating album {$parts[1]}...");
                                    $name = mb_convert_case($parts[1], MB_CASE_TITLE);
                                    $album = $facebook->api('me/albums', 'POST', array('name' => $name));
                                    $albums[$album['id']] = $parts[1];

                                    $item['album'] = $album['id'];
                                }
                                $item['name'] = $parts[2];
                            } else {
                                $item['album'] = true; // default app album
                            }
                            $media = $dropbox->media($entry[0]);
                            $item['url'] = $media['body']->url;
                        } else {
                            $share = $dropbox->shares($entry[0]);
                            $item['url'] = $share['body']->url;
                        }

                        $items[] = $item;
                        $this->log("    {$item['name']} => {$item['url']}");
                    }
                }
            }
        }

        if ($items) {
            $this->log("\nPosting to FB...");
        }
        foreach ($items as $item) {
            $item['name'] = mb_convert_case(
                preg_replace('/\.[a-z]+/i', '', $item['name']) // remove extension
            , MB_CASE_TITLE);

            // Publish photo
            if (!empty($item['album'])) {
                $object_id = $item['album'] === true ? 'me' : $item['album'];
                $this->log("  Photo {$item['name']} @$object_id");
                try {
                    $facebook->api("$object_id/photos", 'POST', array(
                        'url' => $item['url'],
                        'name' => $item['name'],
                    ));
                } catch (FacebookApiException $e) {
                    $this->log("Error: " . $e->getMessage() . "");
                    echo $e->getTraceAsString();
                }

            // Publish wall post
            } else {
                $object_id = empty($item['group']) ? 'me' : $item['group'];
                $this->log("  Wall post {$item['name']} => {$item['url']} @$object_id");
                $facebook->api("$object_id/feed", 'POST', array(
                    'message' => sprintf("Shared file %s: %s", $item['name'], $item['url']),
                ));
            }
        }
        $this->log("\nDone.\n");
    } 
}