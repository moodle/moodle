<?php /// $Id$
      /// Search and replace strings throughout all texts in the whole database

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('multilangupgrade');

$go = optional_param('go', 0, PARAM_BOOL);

###################################################################
admin_externalpage_print_header();

print_heading(get_string('multilangupgrade', 'admin'));

$strmultilangupgrade = get_String('multilangupgradeinfo', 'admin');

if (!$go or !data_submitted() or !confirm_sesskey()) {   /// Print a form
    $optionsyes = array('go'=>1, 'sesskey'=>sesskey());
    notice_yesno($strmultilangupgrade, 'multilangupgrade.php', 'index.php', $optionsyes, null, 'post', 'get');
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

echo '<strong>Progress:</strong>';
$i = 0;
$skiptables = array($CFG->prefix.'config', $CFG->prefix.'user_students', $CFG->prefix.'user_teachers');//, $CFG->prefix.'sessions2');

foreach ($tables as $table) {
    if (($CFG->prefix && strpos($table, $CFG->prefix) !== 0)
      or strpos($table, $CFG->prefix.'pma') === 0) { // Not our tables
        continue;
    }
    if (in_array($table, $skiptables)) { // Don't process these
        continue;
    }
    if ($columns = $db->MetaColumns($table, false)) {
        if (!array_key_exists('id', $columns) and !array_key_exists('ID', $columns)) {
            continue; // moodle tables have id
        }
        foreach ($columns as $column => $data) {
            if (in_array($data->type, array('text','mediumtext','longtext','varchar'))) {  // Text stuff only
                // first find candidate records
                $rs = get_recordset_sql("SELECT id, $column FROM $table WHERE $column LIKE '%</lang>%' OR $column LIKE '%<span lang=%'");
                if ($rs) {
                    while (!$rs->EOF) {
                        $text = $rs->fields[$column];
                        $id   = $rs->fields['id'];

                        if ($i % 600 == 0) {
                            echo '<br />';
                        }
                        if ($i % 10 == 0) {
                            echo '.';
                        }
                        $i++;
                        $rs->MoveNext();

                        if (empty($text) or is_numeric($text)) {
                            continue; // nothing to do
                        }

                        $search = '/(<(?:lang|span) lang="[a-zA-Z0-9_-]*".*?>.+?<\/(?:lang|span)>)(\s*<(?:lang|span) lang="[a-zA-Z0-9_-]*".*?>.+?<\/(?:lang|span)>)+/is';
                        $newtext = preg_replace_callback($search, 'multilangupgrade_impl', $text);

                        if (is_null($newtext)) {
                            continue; // regex error
                        }

                        if ($newtext != $text) {
                            $newtext = addslashes($newtext);
                            execute_sql("UPDATE $table SET $column='$newtext' WHERE id=$id", false);
                        }
                    }
                    rs_close($rs);
                }
            }
        }
    }
}

// set conversion flag - switches to new plugin automatically
set_config('filter_multilang_converted', 1);

print_simple_box_end();

/// Rebuild course cache which might be incorrect now
notify('Rebuilding course cache...', 'notifysuccess');
rebuild_course_cache();
notify('...finished', 'notifysuccess');

print_continue('index.php');

admin_externalpage_print_footer();
die;


function multilangupgrade_impl($langblock) {
    $searchtosplit = '/<(?:lang|span) lang="([a-zA-Z0-9_-]*)".*?>(.+?)<\/(?:lang|span)>/is';
    preg_match_all($searchtosplit, $langblock[0], $rawlanglist);
    $return = '';
    foreach ($rawlanglist[1] as $index=>$lang) {
        $return .= '<span lang="'.$lang.'" class="multilang">'.$rawlanglist[2][$index].'</span>';
    }
    return $return;
}
?>
