<?php // $Id$

function dialogue_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2004111000) {
        execute_sql('DROP INDEX prefix_dialogue_course_idx;',false);
        execute_sql('DROP INDEX prefix_dialogue_conversations_userid_idx;',false);
        execute_sql('DROP INDEX prefix_dialogue_conversations_recipientid_idx;',false);
        execute_sql('DROP INDEX prefix_dialogue_entries_dialogueid_idx;',false);
        execute_sql('DROP INDEX prefix_dialogue_entries_userid_idx;',false);

        modify_database('','CREATE INDEX prefix_dialogue_course_idx ON prefix_dialogue (course);');
        modify_database('','CREATE INDEX prefix_dialogue_conversations_userid_idx ON prefix_dialogue_conversations (userid);');
        modify_database('','CREATE INDEX prefix_dialogue_conversations_recipientid_idx ON prefix_dialogue_conversations (recipientid);');
        modify_database('','CREATE INDEX prefix_dialogue_entries_dialogueid_idx ON prefix_dialogue_entries (dialogueid);');
        modify_database('','CREATE INDEX prefix_dialogue_entries_userid_idx ON prefix_dialogue_entries (userid);');
    }

    $result = true;
    return $result;
}

?>
