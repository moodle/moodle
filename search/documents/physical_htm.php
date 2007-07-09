<?php
/**
* Global Search Engine for Moodle
* add-on 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

function get_text_for_indexing_htm(&$resource){
    global $CFG, $USER;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!isadmin($USER->id)) return;

    // just get text
    $text = implode('', file("{$CFG->dataroot}/{$resource->course}/($resource->reference)"));

    // extract keywords and other interesting meta information and put it back as real content for indexing
    if (preg_match('/(.*)<meta ([^>]*)>(.*)/is',$text, $matches)){
        $prefix = $matches[1];
        $meta_attributes = $matches[2];
        $suffix = $matches{3];
        if (preg_match('/name="(keywords|description)"/i', $attributes)){
            preg_match('/content="[^"]+"/i', $attributes, $matches);
            $text = $prefix.' '.$matches[1].' '.$suffix;
        }
    }
    // filter all html tags
    // $text = clean_text($text, FORMAT_PLAIN);
    // NOTE : this is done in ResourceSearchDocument __constructor
    
    if (!empty($CFG->block_search_limit_index_body)){
        $text = shorten($text, $CFG->block_search_limit_index_body);
    }
    return $text;
}
?>