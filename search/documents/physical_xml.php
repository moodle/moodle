<?php
/**
* Global Search Engine for Moodle
* add-on 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

function get_text_for_indexing_xml(&$resource){
    global $CFG, $USER;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!isadmin($USER->id)) return;

    // just get text
    $text = implode('', file("{$CFG->dataroot}/{$resource->course}/($resource->reference)"));

    // filter out all xml tags
    $text = preg_replace("/<[^>]*>/", ' ', $text);
    
    if (!empty($CFG->block_search_limit_index_body)){
        $text = shorten($text, $CFG->block_search_limit_index_body);
    }
    return $text;
}
?>