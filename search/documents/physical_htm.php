<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage document_wrappers
* @author Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

/**
* @param object $resource
* @uses $CFG
*/
function get_text_for_indexing_htm(&$resource, $directfile = ''){
    global $CFG;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) return;

    // just get text
    if ($directfile == ''){
        $text = implode('', file("{$CFG->dataroot}/{$resource->course}/{$resource->reference}"));
    } else {
        $text = implode('', file("{$CFG->dataroot}/{$directfile}"));
    }

    // extract keywords and other interesting meta information and put it back as real content for indexing
    if (preg_match('/(.*)<meta ([^>]*)>(.*)/is', $text, $matches)){
        $prefix = $matches[1];
        $meta_attributes = $matches[2];
        $suffix = $matches[3];
        if (preg_match('/name="(keywords|description)"/i', $meta_attributes)){
            preg_match('/content="([^"]+)"/i', $meta_attributes, $matches);
            $text = $prefix.' '.$matches[1].' '.$suffix;
        }
    }
    // brutally filters all html tags
    $text = preg_replace("/<[^>]*>/", '', $text);
    $text = preg_replace("/<!--[^>]*-->/", '', $text);
    $text = html_entity_decode($text, ENT_COMPAT, 'UTF-8');
    $text = mb_convert_encoding($text, 'UTF-8', 'auto');
    
    /*
    * debug code for tracing input
    echo "<hr/>";
    $FILE = fopen("filetrace.log", 'w');
    fwrite($FILE, $text);
    fclose($FILE);
    echo "<hr/>";
    */
    
    if (!empty($CFG->block_search_limit_index_body)){
        $text = shorten_text($text, $CFG->block_search_limit_index_body);
    }
    return $text;
}
?>