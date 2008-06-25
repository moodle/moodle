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
* this is a format handler for getting text out of a standard html format 
* so it can be indexed by Lucene search engine
*/

/**
* @param object $resource
*/
function get_text_for_indexing_html(&$resource, $directfile = ''){
    
    // wraps to htm handler
    include_once 'physical_htm.php';
    return get_text_for_indexing_htm($resource, $directfile);
}
?>