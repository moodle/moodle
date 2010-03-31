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
* @note : The Adobe SWF Converters library is not GPL, although it can be of free use in some
* situations. This file is provided for convenience, but should use having a glance at 
* {@link http://www.adobe.com/licensing/developer/}
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

/**
* @param object $resource
* @param string $directfile if the resource is given as a direct file path, use it as reference to the file
* @uses $CFG
*/
function get_text_for_indexing_swf(&$resource, $directfile = ''){
    global $CFG;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) return;
    
    // adds moodle root switch if none was defined
    if (!isset($CFG->block_search_usemoodleroot)){
        set_config('block_search_usemoodleroot', 1);
    }

    $moodleroot = ($CFG->block_search_usemoodleroot) ? "{$CFG->dirroot}/" : '' ;

    // just call pdftotext over stdout and capture the output
    if (!empty($CFG->block_search_pdf_to_text_cmd)){
        $command = trim($CFG->block_search_swf_to_text_cmd);
        if (!file_exists("{$moodleroot}{$command}")){
            mtrace('Error with swf to text converter command : executable not found as '.$moodleroot.$command);
        } else {
            if ($directfile == ''){
                $file = escapeshellarg("{$CFG->dataroot}/{$resource->course}/{$resource->reference}");
            } else {
                $file = escapeshellarg("{$CFG->dataroot}/{$directfile}");
            }
            $text_converter_cmd = "{$moodleroot}{$command} -t $file";
            $result = shell_exec($text_converter_cmd);
            
            // result is in html. We must strip it off
            $result = preg_replace("/<[^>]*>/", '', $result);
            $result = preg_replace("/<!--[^>]*-->/", '', $result);
            $result = html_entity_decode($result, ENT_COMPAT, 'UTF-8');
            $result = mb_convert_encoding($result, 'UTF-8', 'auto');
            
            if ($result){
                return $result;
            } else {
                mtrace('Error with swf to text converter command : execution failed for '.$text_converter_cmd.'. Check for execution permission on swf converter executable.');
                return '';
            }
        }
    } else {
        mtrace('Error with swf to text converter command : command not set up. Execute once search block configuration.');
        return '';
    }
}
?>