<?php
      /// Search and replace strings throughout all texts in the whole database

define('NO_OUTPUT_BUFFERING', true);

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('replace');

$search  = optional_param('search', '', PARAM_RAW);
$replace = optional_param('replace', '', PARAM_RAW);
$sure    = optional_param('sure', 0, PARAM_BOOL);

###################################################################
echo $OUTPUT->header();

echo $OUTPUT->heading('Search and replace text throughout the whole database');

if ($DB->get_dbfamily() !== 'mysql' and $DB->get_dbfamily() !== 'postgres') {
    //TODO: add $DB->text_replace() to DML drivers
    echo $OUTPUT->notification('Sorry, this feature is implemented only for MySQL and PostgreSQL databases.');
    echo $OUTPUT->footer();
    die;
}

if (!data_submitted() or !$search or !$replace or !confirm_sesskey() or !$sure) {   /// Print a form
    echo $OUTPUT->notification('This script is not supported, always make complete backup before proceeding!<br />This operation can not be reverted!');

    echo $OUTPUT->box_start();
    echo '<div class="mdl-align">';
    echo '<form action="replace.php" method="post"><div>';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<div><label for="search">Search whole database for: </label><input id="search" type="text" name="search" size="40" /> (usually previous server URL)</div>';
    echo '<div><label for="replace">Replace with this string: </label><input type="text" id="replace" name="replace" size="40" /> (usually new server URL)</div>';
    echo '<div><label for="sure">I understand the risks of this operation: </label><input type="checkbox" id="sure" name="sure" value="1" /></div>';
    echo '<div class="buttons"><input type="submit" class="singlebutton" value="Yes, do it now" /></div>';
    echo '</div></form>';
    echo '</div>';
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->box_start();
db_replace($search, $replace);
echo $OUTPUT->box_end();

/// Rebuild course cache which might be incorrect now
echo $OUTPUT->notification('Rebuilding course cache...', 'notifysuccess');
rebuild_course_cache();
echo $OUTPUT->notification('...finished', 'notifysuccess');

echo $OUTPUT->continue_button('index.php');

echo $OUTPUT->footer();


