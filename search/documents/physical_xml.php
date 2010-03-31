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
* @version revised for Moodle 2.0
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

/**
* @param object $resource
* @param string $directfile if the resource is given as a direct file path, use it as reference to the file
* @uses $CFG
*/
function get_text_for_indexing_xml(&$resource, $directfile = ''){
    global $CFG;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) return;

    // just get text
    if ($directfile == ''){
        $text = implode('', file("{$CFG->dataroot}/{$resource->course}/{$resource->reference}"));
    } else {
        $text = implode('', file("{$CFG->dataroot}/{$directfile}"));
    }

    // filter out all xml tags
    $text = preg_replace("/<[^>]*>/", ' ', $text);
    
    if (!empty($CFG->block_search_limit_index_body)){
        $text = shorten_text($text, $CFG->block_search_limit_index_body);
    }
    return $text;
}
?>