<?php

function xmldb_block_feedback_install() {
    global $DB;

/// Disable this block by default (because Feedback is not technically part of 2.0)
    $DB->set_field('block', 'visible', 0, array('name'=>'feedback'));

}

