<?php

function xmldb_message_popup_install() {
    global $DB;

    $result = true;

    $provider = new object();
    $provider->name  = 'popup';
    if (!$DB->insert_record('message_processors', $provider)) {
        $return = false;
    }
    return $result;
}
