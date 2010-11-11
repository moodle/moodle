<?php

function xmldb_block_search_install() {
    global $DB;

    // Disable this block by default as its experimental.
    $DB->set_field('block', 'visible', 0, array('name'=>'search'));

}

