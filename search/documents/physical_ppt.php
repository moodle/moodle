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

/*
* first implementation is a trivial heuristic based on ppt character stream :
* text sequence always starts with a 00 9F 0F 04 sequence followed by a 15 bytes
* sequence
* In this sequence is a A8 0F or A0 0F or AA 0F followed by a little-indian encoding of text buffer size
* A8 0F denotes for ASCII text (local system monobyte encoding)
* A0 0F denotes for UTF-16 encoding
* AA 0F are non textual sequences
* texts are either in ASCII or UTF-16
* text ends on a new sequence start, or on a 00 00 NULL UTF-16 end of stream
*
* based on these following rules, here is a little empiric texte extractor for PPT
*/

/**
* @param object $resource
* @param string $directfile if the resource is given as a direct file path, use it as reference to the file
* @uses $CFG
*/
function get_text_for_indexing_ppt(&$resource, $directfile = ''){
    global $CFG;

    $indextext = null;
    
    // SECURITY : do not allow non admin execute anything on system !!
    if (!has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) return;

    if ($directfile == ''){
        $text = implode('', file("{$CFG->dataroot}/{$resource->course}/{$resource->reference}"));
    } else {
        $text = implode('', file("{$CFG->dataroot}/{$directfile}"));
    }
    
    $remains = $text;
    $fragments = array();
    while (preg_match('/\x00\x9F\x0F\x04.{9}(......)(.*)/s', $remains, $matches)){
        $unpacked = unpack("ncode/Llength", $matches[1]);
        $sequencecode = $unpacked['code'];
        $length = $unpacked['length'];
        // print "length : ".$length." ; segment type : ".sprintf("%x", $sequencecode)."<br/>";
        $followup = $matches[2];
        // local system encoding sequence
        if ($sequencecode == 0xA80F){
            $aFragment = substr($followup, 0, $length);
            $remains = substr($followup, $length);
            $fragments[] = $aFragment; 
        }
        // denotes unicode encoded sequence
        elseif ($sequencecode == 0xA00F){
            $aFragment = substr($followup, 0, $length);
            // $aFragment = mb_convert_encoding($aFragment, 'UTF-16', 'UTF-8');
            $aFragment = preg_replace('/\xA0\x00\x19\x20/s', "'", $aFragment); // some quotes
            $aFragment = preg_replace('/\x00/s', "", $aFragment);
            $remains = substr($followup, $length);
            $fragments[] = $aFragment; 
        }
        else{
            $remains = $followup;
        }
    }
    $indextext = implode(' ', $fragments);
    $indextext = preg_replace('/\x19\x20/', "'", $indextext); // some quotes
    $indextext = preg_replace('/\x09/', '', $indextext); // some extra chars
    $indextext = preg_replace('/\x0D/', "\n", $indextext); // some quotes
    $indextext = preg_replace('/\x0A/', "\n", $indextext); // some quotes
    $indextextprint = implode('<hr/>', $fragments);

    // debug code
    // $logppt = fopen("C:/php5/logs/pptlog", "w");
    // fwrite($logppt, $indextext);
    // fclose($logppt);
    
    if (!empty($CFG->block_search_limit_index_body)){
        $indextext = shorten_text($text, $CFG->block_search_limit_index_body);
    }

    $indextext = mb_convert_encoding($indextext, 'UTF-8', 'auto');
    return $indextext;
}
?>