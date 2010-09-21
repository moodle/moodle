<?php
function xmldb_message_jabber_install(){
    global $DB;

    $result = true;

    $provider = new stdClass();
    $provider->name  = 'jabber';
    $DB->insert_record('message_processors', $provider);
    return $result;
}
