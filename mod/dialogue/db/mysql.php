<?PHP // $Id$

function dialogue_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

	if ($oldversion < 2003100500) {
		execute_sql(" ALTER TABLE `{$CFG->prefix}dialogue` ADD `multipleconversations` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' AFTER `dialoguetype`");
		execute_sql(" ALTER TABLE `{$CFG->prefix}dialogue_conversations` ADD `subject` VARCHAR(100) NOT NULL DEFAULT ''");
		}

	if ($oldversion < 2003101300) {
		execute_sql(" ALTER TABLE `{$CFG->prefix}dialogue_conversations` ADD `seenon` INT(10) unsigned NOT NULL DEFAULT '0' AFTER `closed`");
		}

    $result = true;
    return $result;
}

?>
