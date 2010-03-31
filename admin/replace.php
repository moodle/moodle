<?php
      /// Search and replace strings throughout all texts in the whole database

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

// workaround for problems with compression
if (ini_get('zlib.output_compression')) {
    @ini_set('zlib.output_compression', 'Off');
}

admin_externalpage_setup('replace');

$search  = optional_param('search', '', PARAM_RAW);
$replace = optional_param('replace', '', PARAM_RAW);

###################################################################
echo $OUTPUT->header();

echo $OUTPUT->heading('Search and replace text throughout the whole database');


if (!data_submitted() or !$search or !$replace or !confirm_sesskey()) {   /// Print a form

    echo $OUTPUT->box_start();
    echo '<div class="mdl-align">';
    echo '<form action="replace.php" method="post">';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo 'Search whole database for: <input type="text" name="search" /><br />';
    echo 'Replace with this string: <input type="text" name="replace" /><br />';
    echo '<input type="submit" value="Yes, do it now" /><br />';
    echo '</form>';
    echo '</div>';
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    die;
}

echo $OUTPUT->box_start();

if (!db_replace($search, $replace)) {
    print_error('erroroccur', debug);
}

echo $OUTPUT->box_end();

/// Try to replace some well-known serialised contents (html blocks)
echo $OUTPUT->notification('Replacing in html blocks...');
$instances = $DB->get_recordset('block_instances', array('blockname' => 'html'));
foreach ($instances as $instance) {
    $blockobject = block_instance('html', $instance);
    $blockobject->config->text = str_replace($search, $replace, $blockobject->config->text);
    $blockobject->instance_config_commit();
}
$instances->close();

/// Rebuild course cache which might be incorrect now
echo $OUTPUT->notification('Rebuilding course cache...', 'notifysuccess');
rebuild_course_cache();
echo $OUTPUT->notification('...finished', 'notifysuccess');

echo $OUTPUT->continue_button('index.php');

echo $OUTPUT->footer();


