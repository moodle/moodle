<?php /// $Id$
      /// Search and replace strings throughout all texts in the whole database

require('../config.php');

$search  = optional_param('search', '');
$replace = optional_param('replace', '');

require_login();

if (!isadmin()) {
    error("Admins only");
}

###################################################################
print_header('Search and replace throughout the whole database', 'Replace text within the whole database');


if (!$search or !$replace or !confirm_sesskey()) {   /// Print a form

    print_simple_box_start('center');
    echo '<div align="center">';
    echo '<form action="replace.php">';
    echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'">';
    echo 'Search whole database for: <input type="text" name="search"><br />';
    echo 'Replace with this string: <input type="text" name="replace"><br /></br />';
    echo '<input type="submit" value="Yes, do it now"><br />';
    echo '</form>';
    echo '</div>';
    print_simple_box_end();
    die;
}


if (!$tables = $db->Metatables() ) {    // No tables yet at all.
    error("no tables");
}

print_simple_box_start('center');
foreach ($tables as $table) {
    if (in_array($table, array($CFG->prefix.'config'))) {      // Don't process these
        continue;
    }
    if ($columns = $db->MetaColumns($table, false)) {
        foreach ($columns as $column => $data) {
            if (in_array($data->type, array('text','mediumtext','longtext','varchar'))) {  // Text stuff only
                $db->debug = true;
                execute_sql("UPDATE {$CFG->prefix}$table SET $column = REPLACE($column, '$search', '$replace');");
                $db->debug = false;
            }
        }
    }
}
print_simple_box_end();

print_continue('index.php');

?>
