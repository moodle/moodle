<?php
/**
* Global Search Engine for Moodle
* add-on 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

function get_text_for_indexing_html(&$resource){
    
    // wraps to htm handler
    include_once 'physical_htm.php';
    return get_text_for_indexing_htm($resource);
}
?>