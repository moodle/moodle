<?PHP // $Id$

function forum_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

  global $CFG;

  if ($oldversion < 2003042402) {
      execute_sql("INSERT INTO {$CFG->prefix}log_display VALUES ('forum', 'move discussion', 'forum_discussions', 'name')");
  }

  if ($oldversion < 2003082500) {
      table_column("forum", "", "assesstimestart", "integer", "10", "unsigned", "0", "", "assessed");
      table_column("forum", "", "assesstimefinish", "integer", "10", "unsigned", "0", "", "assesstimestart");
  }

  if ($oldversion < 2003082502) {
      execute_sql("UPDATE {$CFG->prefix}forum SET scale = (- scale)");
  }

  if ($oldversion < 2003100600) {
      table_column("forum", "", "maxbytes", "integer", "10", "unsigned", "0", "", "scale");
  }

  if ($oldversion < 2004010100) {
      table_column("forum", "", "assesspublic", "integer", "4", "unsigned", "0", "", "assessed");
  }

  return true;

}



?>

