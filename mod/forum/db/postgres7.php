<?PHP // $Id$

function forum_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

  global $CFG;

  if ($oldversion < 2003042402) {
      execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('forum', 'move discussion', 'forum_discussions', 'name')");
  }

  if ($oldversion < 2003081300) {
      table_column("forum", "", "scale", "integer", "10", "unsigned", "0", "", "assessed");
  }

  return true;

}



?>

