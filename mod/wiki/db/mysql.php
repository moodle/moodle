<?PHP

function wiki_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2004040200) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki` DROP `allowstudentstowiki`');
    }

    if ($oldversion < 2004040700) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki` CHANGE `ewikiallowsafehtml` `htmlmode` TINYINT( 4 ) DEFAULT \'0\' NOT NULL');
    }

    if ($oldversion < 2004042100) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki` ADD `pagename` VARCHAR( 255 ) AFTER `summary`');
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki_entries` CHANGE `name` `pagename` VARCHAR( 255 ) NOT NULL');
        if ($wikis = get_records('wiki')) {
            foreach ($wikis as $wiki) {
                if (empty($wiki->pagename)) {
                    set_field('wiki', 'pagename', $wiki->name, 'id', $wiki->id);
                }
            }
        }
    }

    if ($oldversion < 2004053100) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki` CHANGE `initialcontent` `initialcontent` VARCHAR( 255 ) DEFAULT NULL');
//      Remove obsolete 'initialcontent' values.
        if ($wikis = get_records('wiki')) {
            foreach ($wikis as $wiki) {
                if (!empty($wiki->initialcontent)) {
                    set_field('wiki', 'initialcontent', null, 'id', $wiki->id);
                }
            }
        }
    }

    if ($oldversion < 2004061300) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki`'
                    .' ADD `setpageflags` TINYINT DEFAULT \'1\' NOT NULL AFTER `ewikiacceptbinary`,'
                    .' ADD `strippages` TINYINT DEFAULT \'1\' NOT NULL AFTER `setpageflags`,'
                    .' ADD `removepages` TINYINT DEFAULT \'1\' NOT NULL AFTER `strippages`,'
                    .' ADD `revertchanges` TINYINT DEFAULT \'1\' NOT NULL AFTER `removepages`');
    }

    if ($oldversion < 2004062400) {
        execute_sql('ALTER TABLE `'.$CFG->prefix.'wiki`'
                    .' ADD `disablecamelcase` TINYINT DEFAULT \'0\' NOT NULL AFTER `ewikiacceptbinary`');
    }

    return true;
}

?>
