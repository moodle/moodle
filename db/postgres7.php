<?PHP

function book_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2004060600) {
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                      CHANGE intro summary TEXT NOT NULL;
                     ");
    }
    if ($oldversion < 2004071100) {

        execute_sql ("ALTER TABLE {$CFG->prefix}book_chapters
                      ADD importsrc VARCHAR(255);
                     ");
        execute_sql ("UPDATE {$CFG->prefix}book_chapters
                      SET importsrc = '';
                     ");
        execute_sql ("ALTER TABLE {$CFG->prefix}book_chapters
                      ALTER importsrc SET NOT NULL;
                     ");
        execute_sql ("ALTER TABLE {$CFG->prefix}book_chapters
                      ALTER importsrc SET DEFAULT '';
                     ");
    }
    if ($oldversion < 2004071201) {
        execute_sql ("UPDATE {$CFG->prefix}log_display
                            SET action = 'print'
                            WHERE action = 'prINT';
                     ");
    }
    if ($oldversion < 2004081100) {
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                      ADD disableprinting INT2;
                     ");
        execute_sql ("UPDATE {$CFG->prefix}book
                      SET disableprinting = '0';
                     ");
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                      ALTER disableprinting SET NOT NULL;
                     ");
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                      ALTER disableprinting SET DEFAULT '0';
                     ");
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                      ADD customtitles INT2;
                     ");
        execute_sql ("UPDATE {$CFG->prefix}book
                      SET customtitles = '0';
                     ");
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                      ALTER customtitles SET NOT NULL;
                     ");
        execute_sql ("ALTER TABLE {$CFG->prefix}book
                      ALTER customtitles SET DEFAULT '0';
                     ");
    }

    return true;
}
