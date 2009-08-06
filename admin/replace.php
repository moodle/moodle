<?php /// $Id$
      /// Search and replace strings throughout all texts in the whole database

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('replace');

$search  = optional_param('search', '', PARAM_RAW);
$replace = optional_param('replace', '', PARAM_RAW);

###################################################################
admin_externalpage_print_header();

echo $OUTPUT->heading('Search and replace text throughout the whole database');


if (!data_submitted() or !$search or !$replace or !confirm_sesskey()) {   /// Print a form

    print_simple_box_start('center');
    echo '<div class="mdl-align">';
    echo '<form action="replace.php" method="post">';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo 'Search whole database for: <input type="text" name="search" /><br />';
    echo 'Replace with this string: <input type="text" name="replace" /><br />';
    echo '<input type="submit" value="Yes, do it now" /><br />';
    echo '</form>';
    echo '</div>';
    print_simple_box_end();
    echo $OUTPUT->footer();
    die;
}

print_simple_box_start('center');

if (!db_replace($search, $replace)) {
    print_error('erroroccur', debug);
}

print_simple_box_end();

/// Try to replace some well-known serialised contents (html blocks)
notify('Replacing in html blocks...');
$instances = $DB->get_recordset('block_instances', array('blockname' => 'html'));
foreach ($instances as $instance) {
    $blockobject = block_instance('html', $instance);
    $blockobject->config->text = str_replace($search, $replace, $blockobject->config->text);
    $blockobject->instance_config_commit();
}
$instances->close();

/// Rebuild course cache which might be incorrect now
notify('Rebuilding course cache...', 'notifysuccess');
rebuild_course_cache();
notify('...finished', 'notifysuccess');

print_continue('index.php');

echo $OUTPUT->footer();

?>
