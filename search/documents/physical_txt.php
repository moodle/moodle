<?php
/**
* Global Search Engine for Moodle
* add-on 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

function get_text_for_indexing_txt(&$resource){
    global $CFG, $USER;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!isadmin($USER->id)) return;

    // just try to get text empirically from ppt binary flow
    $text = implode('', file("{$CFG->dataroot}/{$resource->course}/{$resource->reference}"));
    if (!empty($CFG->block_search_limit_index_body)){
        $text = shorten($text, $CFG->block_search_limit_index_body);
    }
    return $text;
}
?>