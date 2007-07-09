<?php
/**
* Global Search Engine for Moodle
* add-on 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

function get_text_for_indexing_pdf(&$resource){
    global $CFG, $USER;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!isadmin($USER->id)) return;

    // just call pdftotext over stdout and capture the output
    if (!empty($CFG->block_search_pdf_to_text_cmd)){
        preg_match("/^\S+/", $CFG->block_search_pdf_to_text_cmd, $matches);
        if (!file_exists("{$CFG->dirroot}/{$matches[0]}")){
            mtrace('Error with pdf to text converter command : exectuable not found.');
        }
        else{
            $file = $CFG->dataroot.'/'.$resource->course.'/'.$resource->reference;
            $text_converter_cmd = "{$CFG->dirroot}/{$CFG->block_search_pdf_to_text_cmd} $file -";
            $result = shell_exec($text_converter_cmd);
            if ($result){
                return $result;
            }
            else{
                mtrace('Error with pdf to text converter command : execution failed.');
                return '';
            }
        }
    } 
    else {
        mtrace('Error with pdf to text converter command : command not set up. Execute once search block configuration.');
        return '';
    }
}
?>