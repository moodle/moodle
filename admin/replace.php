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

print_heading('Search and replace text throughout the whole database');


if (!data_submitted() or !$search or !$replace or !confirm_sesskey()) {   /// Print a form

    print_simple_box_start('center');
    echo '<div align="center">';
    echo '<form action="replace.php" method="post">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
    echo 'Search whole database for: <input type="text" name="search" /><br />';
    echo 'Replace with this string: <input type="text" name="replace" /><br />';
    echo '<input type="submit" value="Yes, do it now" /><br />';
    echo '</form>';
    echo '</div>';
    print_simple_box_end();
    admin_externalpage_print_footer();
    die;
}


if (!$tables = $db->Metatables() ) {    // No tables yet at all.
    error("no tables");
}

print_simple_box_start('center');

/// Turn off time limits, sometimes upgrades can be slow.

@set_time_limit(0);
@ob_implicit_flush(true);
while(@ob_end_flush());

foreach ($tables as $table) {
    if (in_array($table, array($CFG->prefix.'config'))) {      // Don't process these
        continue;
    }
    if ($columns = $db->MetaColumns($table, false)) {
        foreach ($columns as $column => $data) {
            if (in_array($data->type, array('text','mediumtext','longtext','varchar'))) {  // Text stuff only
                $db->debug = true;
                execute_sql("UPDATE $table SET $column = REPLACE($column, '$search', '$replace');");
                $db->debug = false;
            }
        }
    }
}

print_simple_box_end();

/// Rebuild course cache which might be incorrect now
notify('Rebuilding course cache...');
rebuild_course_cache();
notify('...finished');

print_continue('index.php');

admin_externalpage_print_footer();

?>
