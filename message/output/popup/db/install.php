<?php

function xmldb_message_popup_install() {
    global $DB;

    $result = true;

    $provider = new stdClass();
    $provider->name  = 'popup';
    $DB->insert_record('message_processors', $provider);
    return $result;
}
