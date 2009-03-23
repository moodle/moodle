<?php
/*
* Moodle global search engine
* This is a special externalized code for cron handling in PHP5.
* Should never be called by a php 4.3.0 implementation. 
* @package search
* @category core
* @subpackage search_engine
* @author Michael Champanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @version prepared for 2.0
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*/

try{
    // overrides php limits
    $maxtimelimit = ini_get('max_execution_time');
    ini_set('max_execution_time', 600);
    $maxmemoryamount = ini_get('memory_limit');
    ini_set('memory_limit', '96M');

    mtrace("\n--DELETE----");
    require_once($CFG->dirroot.'/search/delete.php');
    mtrace("--UPDATE----");
    require_once($CFG->dirroot.'/search/update.php');
    mtrace("--ADD-------");
    require_once($CFG->dirroot.'/search/add.php');
    mtrace("------------");
    //mtrace("cron finished.</pre>");
    mtrace('done');

    // set back normal values for php limits
    ini_set('max_execution_time', $maxtimelimit);
    ini_set('memory_limit', $maxmemoryamount);
}
catch(Exception $ex){
    mtrace('Fatal exception from Lucene subsystem. Search engine may not have been updated.');
    mtrace($ex);
}
?>