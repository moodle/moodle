<?php
/**
* Global Search Engine for Moodle
*
* @package search
* @category core
* @subpackage search_engine
* @author Michael Champanis (mchampan) [cynnical@gmail.com], Valery Fremaux [valery.fremaux@club-internet.fr] > 1.8
* @date 2008/03/31
* @version prepared for 2.0
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
*
* Prints some basic statistics about the current index.
* Does some diagnostics if you are logged in as an administrator.
*
*/

/**
* includes and requires
*/
require_once('../config.php');
require_once($CFG->dirroot.'/search/lib.php');

$block_instanceid = required_param('block_instanceid', PARAM_INT);// Block Instance ID

/// checks global search is enabled

    if ($CFG->forcelogin) {
        require_login();
    }

    if (empty($CFG->enableglobalsearch)) {
        print_error('globalsearchdisabled', 'search');
    }
    //Check user's permissions against the block instance from which the user came
    if (empty($block_instanceid)) {
        print_error('searchnotpermitted', 'search');
    }
    if (!$DB->record_exists('block_instances', array('id' => $block_instanceid, 'blockname' => 'search'))) {
        print_error('searchnotpermitted', 'search');
    }
    $contextblock = get_context_instance(CONTEXT_BLOCK, $block_instanceid);
    require_capability('moodle/block:view', $contextblock);

/// check for php5, but don't die yet

    require_once($CFG->dirroot.'/search/indexlib.php');

    $indexinfo = new IndexInfo();

    $site = get_site();

    $strsearch = get_string('search', 'search');
    $strquery  = get_string('statistics', 'search');

    $site = get_site();

    $url = new moodle_url('/search/stats.php');
    $url->param('block_instanceid', $block_instanceid);
    $PAGE->set_url($url);

    $PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
    $PAGE->navbar->add($strsearch, new moodle_url('/search/query.php?block_instanceid=' . $block_instanceid));
    $PAGE->navbar->add($strquery, new moodle_url('/search/stats.php?block_instanceid=' . $block_instanceid));
    $PAGE->set_title($strsearch);
    $PAGE->set_heading($site->fullname);
    echo $OUTPUT->header();

/// keep things pretty, even if php5 isn't available

    echo $OUTPUT->box_start();
    echo $OUTPUT->heading($strquery);

    echo $OUTPUT->box_start();

    $databasestr = get_string('database', 'search');
    $documentsinindexstr = get_string('documentsinindex', 'search');
    $deletionsinindexstr = get_string('deletionsinindex', 'search');
    $documentsindatabasestr = get_string('documentsindatabase', 'search');
    $databasestatestr = get_string('databasestate', 'search');

/// this table is only for admins, shows index directory size and location

    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
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

        $admin_table = new html_table();
        $admin_table->tablealign = 'center';
        $admin_table->align = array ('right', 'left');
        $admin_table->wrap = array ('nowrap', 'nowrap');
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

        echo html_writer::table($admin_table);
        $spacer = array('height'=>20, 'br'=>true);
        echo $OUTPUT->spacer($spacer); // should be done with CSS instead
        echo $OUTPUT->heading($solutionsstr);

        unset($admin_table->data);
        if (isset($errors['dir'])) {
            $admin_table->data[] = array($checkdirstr, $checkdiradvicestr);
        }
        if (isset($errors['db'])) {
            $admin_table->data[] = array($checkdbstr, $checkdbadvicestr);
        }

        $admin_table->data[] = array($runindexerteststr, '<a href="tests/index.php" target="_blank">tests/index.php</a>');
        $admin_table->data[] = array($runindexerstr, '<a href="indexersplash.php" target="_blank">indexersplash.php</a>');

        echo html_writer::table($admin_table);
        echo $OUTPUT->spacer($spacer) . '<br />';
    }

/// this is the standard summary table for normal users, shows document counts

    $table = new html_table();
    $table->tablealign = 'center';
    $table->align = array ('right', 'left');
    $table->wrap = array ('nowrap', 'nowrap');
    $table->cellpadding = 5;
    $table->cellspacing = 0;
    $table->width = '500';

    $table->data[] = array("<strong>{$databasestr}</strong>", "<em><strong>{$CFG->prefix}".SEARCH_DATABASE_TABLE.'</strong></em>');

/// add extra fields if we're admin

    if (has_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM))) {
        //don't want to confuse users if the two totals don't match (hint: they should)
        $table->data[] = array($documentsinindexstr, $indexinfo->indexcount);

        //*cough* they should match if deletions were actually removed from the index,
        //as it turns out, they're only marked as deleted and not returned in search results
        $table->data[] = array($deletionsinindexstr, (int)$indexinfo->indexcount - (int)$indexinfo->dbcount);
    }

    $table->data[] = array($documentsindatabasestr, $indexinfo->dbcount);

    foreach($indexinfo->types as $type) {
        if ($type->type == 'mod'){
            $table->data[] = array(get_string('documentsfor', 'search') . " '".get_string('modulenameplural', $type->name)."'", $type->records);
        } else if ($type->type == 'block') {
            $table->data[] = array(get_string('documentsfor', 'search') . " '".get_string('pluginname', $type->name)."'", $type->records);
        } else {
            $table->data[] = array(get_string('documentsfor', 'search') . " '".get_string($type->name)."'", $type->records);
        }

    }

    echo $OUTPUT->heading($databasestatestr);
    echo html_writer::table($table);

    echo $OUTPUT->box_end();
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
?>
