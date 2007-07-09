<?php
/**
* Global Search Engine for Moodle
* add-on 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* this is a format handler for getting text out of a proprietary binary format 
* so it can be indexed by Lucene search engine
*/

/*
* MS Word extractor
*/

function get_text_for_indexing_doc(&$resource){
    global $CFG, $USER;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!isadmin($USER->id)) return;

    // just call pdftotext over stdout and capture the output
    if (!empty($CFG->block_search_word_to_text_cmd)){
        if (!file_exists("{$CFG->dirroot}/{$CFG->block_search_word_to_text_cmd}")){
            mtrace('Error with MSWord to text converter command : exectuable not found.');
        }
        else{
            $file = $CFG->dataroot.'/'.$resource->course.'/'.$resource->reference;
            $text_converter_cmd = "{$CFG->dirroot}/{$CFG->block_search_word_to_text_cmd} $file";
            if ($CFG->block_search_word_to_text_env){
                putenv($CFG->block_search_word_to_text_env);
            }
            $result = shell_exec($text_converter_cmd);
            if ($result){
                return mb_convert_encoding($result, 'UTF8', 'auto');
            }
            else{
                mtrace('Error with MSWord to text converter command : execution failed.');
                return '';
            }
        }
    } 
    else {
        mtrace('Error with MSWord to text converter command : command not set up. Execute once search block configuration.');
        return '';
    }
}
?>