<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage document_wrappers
* @author Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @contributor Tatsuva Shirai 20090530
* @date 2008/03/31
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

/**
* MS Word extractor
* @param object $resource 
* @uses $CFG
*/
function get_text_for_indexing_doc(&$resource, $directfile = ''){
    global $CFG;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) return;

    $moodleroot = (@$CFG->block_search_usemoodleroot) ? "{$CFG->dirroot}/" : '' ;

    // just call antiword over stdout and capture the output
    if (!empty($CFG->block_search_word_to_text_cmd)){
        // we need to remove any line command options...
        preg_match("/^\S+/", $CFG->block_search_word_to_text_cmd, $matches);
        if (!file_exists("{$moodleroot}{$matches[0]}")){
            mtrace('Error with MSWord to text converter command : executable not found at '.$moodleroot.$CFG->block_search_word_to_text_cmd);
        } else {
            if ($directfile == ''){
                $file = escapeshellarg("{$CFG->dataroot}/{$resource->course}/{$resource->reference}");
            } else {
                $file = escapeshellarg("{$CFG->dataroot}/{$directfile}");
            }
            $command = trim($CFG->block_search_word_to_text_cmd);
            $text_converter_cmd = "{$moodleroot}{$command} -m UTF-8.txt $file";
            if ($CFG->block_search_word_to_text_env){
                putenv($CFG->block_search_word_to_text_env);
            }
            mtrace("Executing : $text_converter_cmd");
            $result = shell_exec($text_converter_cmd);
            if ($result){
                return mb_convert_encoding($result, 'UTF-8', 'auto');
            } else {
                mtrace('Error with MSWord to text converter command : execution failed. ');
                return '';
            }
        }
    } else {
        mtrace('Error with MSWord to text converter command : command not set up. Execute once search block configuration.');
        return '';
    }
}
?>