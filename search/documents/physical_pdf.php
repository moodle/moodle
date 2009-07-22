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
function get_text_for_indexing_pdf(&$resource, $directfile = ''){
    global $CFG;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!has_capability('moodle/site:doanything', get_context_instance(CONTEXT_SYSTEM))) return;
    
    // adds moodle root switch if none was defined
    if (!isset($CFG->block_search_usemoodleroot)){
        set_config('block_search_usemoodleroot', 1);
    }

    $moodleroot = ($CFG->block_search_usemoodleroot) ? "{$CFG->dirroot}/" : '' ;

    // just call pdftotext over stdout and capture the output
    if (!empty($CFG->block_search_pdf_to_text_cmd)){
        // we need to remove any line command options...
        preg_match("/^\S+/", $CFG->block_search_pdf_to_text_cmd, $matches);
        if (!file_exists("{$moodleroot}{$matches[0]}")){
            mtrace('Error with pdf to text converter command : executable not found at '.$moodleroot.$matches[0]);
        } else {
            if ($directfile == ''){
                $file = escapeshellarg("{$CFG->dataroot}/{$resource->course}/{$resource->reference}");
            } else {
                $file = escapeshellarg("{$CFG->dataroot}/{$directfile}");
            }
            $command = trim($CFG->block_search_pdf_to_text_cmd);
            $text_converter_cmd = "{$moodleroot}{$command} $file -";
            $result = shell_exec($text_converter_cmd);
            if ($result){
                return $result;
            } else {
                mtrace('Error with pdf to text converter command : execution failed for '.$text_converter_cmd.'. Check for execution permission on pdf converter executable.');
                return '';
            }
        }
    } else {
        mtrace('Error with pdf to text converter command : command not set up. Execute once search block configuration.');
        return '';
    }
}
?>