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
    ini_set('max_execution_time', 300);
    raise_memory_limit(MEMORY_EXTRA);

    mtrace("\n--DELETE----");
    require_once($CFG->dirroot.'/search/delete.php');
    mtrace("--UPDATE----");
    require_once($CFG->dirroot.'/search/update.php');
    mtrace("--ADD-------");
    require_once($CFG->dirroot.'/search/add.php');
    mtrace("------------");
    //mtrace("cron finished.</pre>");
    mtrace('done');
}
catch(Exception $ex){
    mtrace('Fatal exception from Lucene subsystem. Search engine may not have been updated.');
    mtrace($ex);
}
?>
