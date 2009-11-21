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
function get_text_for_indexing_txt(&$resource, $directfile = ''){
    global $CFG;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) return;

    // just try to get text empirically from ppt binary flow
    if ($directfile == ''){
        $text = implode('', file("{$CFG->dataroot}/{$resource->course}/{$resource->reference}"));
    } else {
        $text = implode('', file("{$CFG->dataroot}/{$directfile}"));
    }
    
    if (!empty($CFG->block_search_limit_index_body)){
        $text = shorten_text($text, $CFG->block_search_limit_index_body);
    }
    return $text;
}
?>