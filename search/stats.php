<?php
/**
* Global Search Engine for Moodle
* Michael Champanis (mchampan) [cynnical@gmail.com]
* review 1.8+ : Valery Fremaux [valery.fremaux@club-internet.fr] 
* 2007/08/02
*
* Prints some basic statistics about the current index.
* Does some diagnostics if you are logged in as an administrator.
* 
*/

require_once('../config.php');
require_once("{$CFG->dirroot}/search/lib.php");

if ($CFG->forcelogin) {
    require_login();
}

if (empty($CFG->enableglobalsearch)) {
    error(get_string('globalsearchdisabled', 'search'));
}

//check for php5, but don't die yet
if ($check = search_check_php5()) {
    require_once("{$CFG->dirroot}/search/indexlib.php");
    
    $indexinfo = new IndexInfo();
} 

if (!$site = get_site()) {
    redirect("index.php");
} 

$strsearch = get_string('search', 'search');
$strquery  = get_string('statistics', 'search'); 

print_header("$site->shortname: $strsearch: $strquery", "$site->fullname",
           "<a href=\"index.php\">$strsearch</a> -> $strquery");

//keep things pretty, even if php5 isn't available
if (!$check) {
    print_heading(search_check_php5(true));
    print_footer();
    exit(0);
}

print_box_start();
print_heading($strquery);

print_box_start();

$databasestr = get_string('database', 'search');
$documentsinindexstr = get_string('documentsinindex', 'search');
$deletionsinindexstr = get_string('deletionsinindex', 'search');
$documentsindatabasestr = get_string('documentsindatabase', 'search');
$databasestatestr = get_string('databasestate', 'search');

//this table is only for admins, shows index directory size and location
if (isadmin()) {
    $datadirectorystr = get_string('datadirectory', 'search');
    $inindexdirectorystr = get_string('filesinindexdirectory', 'search');
    $totalsizestr = get_string('totalsize', 'search');
    $errorsstr = get_string('errors', 'search');
    $solutionsstr = get_string('solutions', 'search');
    $checkdirstr = get_string('checkdir', 'search');
    $checkdbstr = get_string('checkdb', 'search');
    $checkdiradvicestr = get_string('checkdiradvice', 'search');
    $checkdbadvicestr = get_string('checkdbadvice', 'search');
    $runindexerteststr = get_string('runindexertest', 'search');
    $runindexerstr = get_string('runindexer', 'search');
    
    $admin_table->tablealign = "center";
    $admin_table->align = array ("right", "left");
    $admin_table->wrap = array ("nowrap", "nowrap");
    $admin_table->cellpadding = 5;
    $admin_table->cellspacing = 0;
    $admin_table->width = '500';

    $admin_table->data[] = array("<strong>{$datadirectorystr}</strong>", '<em><strong>'.$indexinfo->path.'</strong></em>');
    $admin_table->data[] = array($inindexdirectorystr, $indexinfo->filecount);
    $admin_table->data[] = array($totalsizestr, $indexinfo->size);

    if ($indexinfo->time > 0) {
        $admin_table->data[] = array(get_string('createdon', 'search'), date('r', $indexinfo->time));
    } 
    else {
        $admin_table->data[] = array(get_string('createdon', 'search'), '-');
    } 

    if (!$indexinfo->valid($errors)) {
        $admin_table->data[] = array("<strong>{$errorsstr}</strong>", '&nbsp;');
        foreach ($errors as $key => $value) {
            $admin_table->data[] = array($key.' ... ', $value);
        } 
    }

    print_table($admin_table);
    print_spacer(20);
    print_heading($solutionsstr);
    
    unset($admin_table->data);
    if (isset($errors['dir'])) {
        $admin_table->data[] = array($checkdirstr, $checkdiradvicestr);
    } 
    if (isset($errors['db'])) {
        $admin_table->data[] = array($checkdbstr, $checkdbadvicestr);
    } 
    
    $admin_table->data[] = array($runindexerteststr, '<a href="tests/index.php" target="_blank">tests/index.php</a>');
    $admin_table->data[] = array($runindexerstr, '<a href="indexersplash.php" target="_blank">indexersplash.php</a>');
    
    print_table($admin_table);
    print_spacer(20);
} 

//this is the standard summary table for normal users, shows document counts
$table->tablealign = "center";
$table->align = array ("right", "left");
$table->wrap = array ("nowrap", "nowrap");
$table->cellpadding = 5;
$table->cellspacing = 0;
$table->width = '500';

$table->data[] = array("<strong>{$databasestr}</strong>", "<em><strong>{$CFG->prefix}search_documents</strong></em>");

//add extra fields if we're admin
if (isadmin()) {
    //don't want to confuse users if the two totals don't match (hint: they should)
    $table->data[] = array($documentsinindexstr, $indexinfo->indexcount);
    
    //*cough* they should match if deletions were actually removed from the index,
    //as it turns out, they're only marked as deleted and not returned in search results
    $table->data[] = array($deletionsinindexstr, (int)$indexinfo->indexcount - (int)$indexinfo->dbcount);
} 

$table->data[] = array($documentsindatabasestr, $indexinfo->dbcount);

foreach($indexinfo->types as $key => $value) {
    $table->data[] = array(get_string('documentsfor', 'search') . " '".get_string('modulenameplural', $key)."'", $value);
} 

print_heading($databasestatestr);
print_table($table);

print_box_end();
print_box_end();
print_footer();
?>