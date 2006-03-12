<?PHP // $Id: mysql.php,v 1.1 2006/03/12 18:40:01 skodak Exp $

function book_upgrade($oldversion) {
/// This function does anything necessary to upgrade
/// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2004060600) {
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                            CHANGE intro summary TEXT NOT NULL;
                     ");
    }
    if ($oldversion < 2004071100) {
        execute_sql ("ALTER TABLE {$CFG->prefix}book_chapters
                            ADD importsrc VARCHAR(255) NOT NULL DEFAULT '' AFTER timemodified;
                     ");
    }
    if ($oldversion < 2004071201) {
        execute_sql ("UPDATE {$CFG->prefix}log_display
                            SET action = 'print'
                            WHERE action = 'prINT';
                     ");
    }
    if ($oldversion < 2004072400) {
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                            ADD disableprinting TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' AFTER numbering;
                     ");
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                            ADD customtitles TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' AFTER disableprinting;
                     ");
    }
    return true;
}

?>
