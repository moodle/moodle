<?PHP // $Id$

function choice_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    if ($oldversion < 2003010100) {
        execute_sql(" ALTER TABLE `choice` ADD `format` INTEGER DEFAULT '0' NOT NULL AFTER `text` ");
        execute_sql(" ALTER TABLE `choice` ADD `publish` INTEGER DEFAULT '0' NOT NULL AFTER `answer6` ");
    }

    return true;
}


?>

