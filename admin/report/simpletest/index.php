<?php
/**
 * Run the unit tests.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear@open.ac.uk, T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version $Id$
 * @package SimpleTestEx
 */

/** */
require_once(dirname(__FILE__).'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/simpletestlib.php');
require_once('ex_simple_test.php');
require_once('ex_reporter.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$langfile = 'simpletest';
$unittest = true;

// CGI arguments
$path = optional_param('path', null, PARAM_PATH);
$showpasses = optional_param('showpasses', false, PARAM_BOOL);
$showsearch = optional_param('showsearch', false, PARAM_BOOL);
$rundbtests = optional_param('rundbtests', false, PARAM_BOOL);
$thorough = optional_param('thorough', false, PARAM_BOOL);
$addconfigprefix = optional_param('addconfigprefix', false, PARAM_RAW);
$setuptesttables = optional_param('setuptesttables', false, PARAM_BOOL);
$continuesetuptesttables = optional_param('continuesetuptesttables', false, PARAM_BOOL);
$droptesttables = optional_param('droptesttables', false, PARAM_BOOL);
$testtablesok = optional_param('testtablesok', false, PARAM_BOOL);

global $UNITTEST;
$UNITTEST = new object();

// Print the header.
admin_externalpage_setup('reportsimpletest');
$strtitle = get_string('unittests', $langfile);
admin_externalpage_print_header();

if ($testtablesok) {
    print_heading(get_string('testtablesok', 'simpletest'));
}

$baseurl = $CFG->wwwroot . '/admin/report/simpletest/index.php';

// Add unittest prefix to config.php if needed
if ($addconfigprefix && !isset($CFG->unittestprefix)) {
    // Open config file, search for $CFG->prefix and append a new line under it
    $handle = fopen($CFG->dirroot.'/config.php', 'r+');

    $new_file = '';

    while (!feof($handle)) {
        $line = fgets($handle, 4096);
        $prefix_line = null;

        if (preg_match('/CFG\-\>prefix/', $line, $matches)) {
            $prefix_line = "\$CFG->unittestprefix = '$addconfigprefix';\n";
        }

        $new_file .= $line;
        $new_file .= $prefix_line;
    }

    fclose($handle);
    $handle = fopen($CFG->dirroot.'/config.php', 'w');
    fwrite($handle, $new_file);
    fclose($handle);
    $CFG->unittestprefix = $addconfigprefix;
}

if (empty($CFG->unittestprefix)) {
    // TODO replace error with proper admin dialog
    print_box_start('generalbox', 'notice');
    echo '<p>'.get_string("prefixnotset", 'simpletest').'</p>';
    echo '<form method="post" action="'.$baseurl.'">
            <table class="generaltable">
                <tr>
                    <th class="header"><label for="prefix">'.get_string('prefix', 'simpletest').'</label></th>
                    <td class="cell"><input type="text" size="5" name="addconfigprefix" id="prefix" value="tst_" /></td>
                    <td class="cell"><input type="submit" value="'.get_string('addconfigprefix', 'simpletest').'" /></td>
                </tr>
            </table>
          </form>';
    print_box_end();
    admin_externalpage_print_footer();
    exit();
}

// Temporarily override $DB and $CFG for a fresh install on the unit test prefix
$real_db = clone($DB);
$real_cfg = clone($CFG);
$CFG = new stdClass();
$CFG->dbhost              = $real_cfg->dbhost;
$CFG->dbtype              = $real_cfg->dbtype;
$CFG->dblibrary           = $real_cfg->dblibrary;
$CFG->dbuser              = $real_cfg->dbuser;
$CFG->dbpass              = $real_cfg->dbpass;
$CFG->dbname              = $real_cfg->dbname;
$CFG->dbpersist           = $real_cfg->dbpersist;
$CFG->unittestprefix      = $real_cfg->unittestprefix;
$CFG->wwwroot             = $real_cfg->wwwroot;
$CFG->dirroot             = $real_cfg->dirroot;
$CFG->libdir              = $real_cfg->libdir;
$CFG->dataroot            = $real_cfg->dataroot;
$CFG->admin               = $real_cfg->admin;
$CFG->release             = $real_cfg->release;
$CFG->version             = $real_cfg->version;
$CFG->config_php_settings = $real_cfg->config_php_settings;
$CFG->frametarget         = $real_cfg->frametarget;
$CFG->framename           = $real_cfg->framename;
$CFG->footer              = $real_cfg->footer;
$CFG->debug               = $real_cfg->debug;

$DB = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);
$DB->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->dbpersist, $CFG->unittestprefix);

if ($DB->get_manager()->table_exists(new xmldb_table('config')) && $config = $DB->get_records('config')) {
    foreach ($config as $conf) {
        $CFG->{$conf->name} = $conf->value;
    }
}

$test_tables = $DB->get_tables();

// Build test tables if requested and needed
if ($setuptesttables || $continuesetuptesttables) {
    $version = null;
    $release = null;
    include("$CFG->dirroot/version.php");       // defines $version and $release

    if (!$continuesetuptesttables) {
        // Drop all tables first if they exist
        $manager = $DB->get_manager();
        foreach ($test_tables as $table) {
            $xmldbtable = new xmldb_table($table);
            $manager->drop_table($xmldbtable);
        }
    }

    upgrade_db($version, $release, true);
}

if ($droptesttables) {
    $manager = $DB->get_manager();
    foreach ($test_tables as $table) {
        $xmldbtable = new xmldb_table($table);
        $manager->drop_table($xmldbtable);
    }
    $test_tables = $DB->get_tables();
}

if (empty($test_tables['config'])) {
    // TODO replace error with proper admin dialog
    notice_yesno(get_string('tablesnotsetup', 'simpletest'), $baseurl . '?setuptesttables=1', $baseurl);
    $DB = $real_db;
    admin_externalpage_print_footer();
    exit();
}

$DB = $real_db;
$CFG = $real_cfg;

if (!is_null($path)) {
    // Create the group of tests.
    $test = new AutoGroupTest($showsearch, $thorough);

    // OU specific. We use the _nonproject folder for stuff we want to
    // keep in CVS, but which is not really relevant. It does no harm
    // to leave this here.
    $test->addIgnoreFolder($CFG->dirroot . '/_nonproject');
    $test->addIgnoreFolder($CFG->libdir . '/ddl');
    $test->addIgnoreFolder($CFG->libdir . '/dml');

    // Make the reporter, which is what displays the results.
    $reporter = new ExHtmlReporter($showpasses);

    if ($showsearch) {
        print_heading('Searching for test cases');
    }
    flush();

    // Work out what to test.
    if (substr($path, 0, 1) == '/') {
        $path = substr($path, 1);
    }
    $path = $CFG->dirroot . '/' . $path;
    if (substr($path, -1) == '/') {
        $path = substr($path, 0, -1);
    }
    $displaypath = substr($path, strlen($CFG->dirroot) + 1);
    $ok = true;
    if (is_file($path)) {
        $test->addTestFile($path);
    } else if (is_dir($path)){
        $test->findTestFiles($path);
    } else {
        print_simple_box(get_string('pathdoesnotexist', $langfile, $path), '', '', '', '', 'errorbox');
        $ok = false;
    }

    // Add ddl and dml tests if requested
    if ($rundbtests) {
        if (!strstr($path, $CFG->libdir . '/ddl')) {
            $test->addTestFile($CFG->libdir . '/ddl/simpletest/testddl.php');
        }
        if (!strstr($path, $CFG->libdir . '/dml')) {
            $test->addTestFile($CFG->libdir . '/dml/simpletest/testdml.php');
        }
    }

    // If we have something to test, do it.
    if ($ok) {
        if ($path == $CFG->dirroot) {
            $title = get_string('moodleunittests', $langfile, get_string('all', $langfile));
        } else {
            $title = get_string('moodleunittests', $langfile, $displaypath);
        }
        print_heading($title);
        $test->run($reporter);
    }

    $formheader = get_string('retest', $langfile);
} else {
    $displaypath = '';
    $formheader = get_string('rununittests', $langfile);
}
// Print the form for adjusting options.
print_box_start('generalbox boxwidthwide boxaligncenter');
echo '<form method="get" action="index.php">';
echo '<fieldset class="invisiblefieldset">';
print_heading($formheader);
echo '<p>'; print_checkbox('showpasses', 1, $showpasses, get_string('showpasses', $langfile)); echo '</p>';
echo '<p>'; print_checkbox('showsearch', 1, $showsearch, get_string('showsearch', $langfile)); echo '</p>';
echo '<p>'; print_checkbox('thorough', 1, $thorough, get_string('thorough', $langfile)); echo '</p>';
echo '<p>';
    echo '<label for="path">', get_string('onlytest', $langfile), '</label> ';
    echo '<input type="text" id="path" name="path" value="', $displaypath, '" size="40" />';
echo '</p>';
echo '<p>'; print_checkbox('rundbtests', 1, $rundbtests, get_string('rundbtests', $langfile)); echo '</p>'; // TODO: localise
echo '<input type="submit" value="' . get_string('runtests', $langfile) . '" />';
echo '</fieldset>';
echo '</form>';
print_box_end();

// Footer.
admin_externalpage_print_footer();

?>
