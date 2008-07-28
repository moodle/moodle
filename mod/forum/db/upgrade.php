<?php  //$Id$

// This file keeps track of upgrades to
// the forum module
//
// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.
//
// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installtion to the current version.
//
// If there's something it cannot do itself, it
// will tell you what you need to do.
//
// The commands in here will all be database-neutral,
// using the methods of database_manager class

function xmldb_forum_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    $result = true;

/// And upgrade begins here. For each one, you'll need one
/// block of code similar to the next one. Please, delete
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of database_manager methods
/// }

//===== 1.9.0 upgrade line ======//

    if ($result and $oldversion < 2007101511) {
        notify('Processing forum grades, this may take a while if there are many forums...', 'notifysuccess');
        //MDL-13866 - send forum ratins to gradebook again
        require_once($CFG->dirroot.'/mod/forum/lib.php');
        // too much debug output
        $DB->set_debug(false);
        forum_update_grades();
        $DB->set_debug(true);

        upgrade_mod_savepoint($result, 2007101511, 'forum');
    }

    if ($result && $oldversion < 2007101512) {

    /// Cleanup the forum subscriptions
        notify('Removing stale forum subscriptions', 'notifysuccess');

        $roles = get_roles_with_capability('moodle/course:view', CAP_ALLOW);
        $roles = array_keys($roles);

        list($usql, $params) = $DB->get_in_or_equal($roles);
        $sql = "SELECT fs.userid, f.id AS forumid
                  FROM {forum} f
                       JOIN {course} c                 ON c.id = f.course
                       JOIN {context} ctx              ON (ctx.instanceid = c.id AND ctx.contextlevel = ".CONTEXT_COURSE.")
                       JOIN {forum_subscriptions} fs   ON fs.forum = f.id
                       LEFT JOIN {role_assignments} ra ON (ra.contextid = ctx.id AND ra.userid = fs.userid AND ra.roleid $usql)
                 WHERE ra.id IS NULL";

        if ($rs = $DB->get_recordset_sql($sql, $params)) {
            $DB->set_debug(false);
            foreach ($rs as $remove) {
                $DB->delete_records('forum_subscriptions', array('userid'=>$remove->userid, 'forum'=>$remove->forumid));
                echo '.';
            }
            $DB->set_debug(true);
            $rs->close();
        }

        upgrade_mod_savepoint($result, 2007101512, 'forum');
    }

    if ($result and $oldversion < 2008072401) {
        $eventdata = new object();
        $eventdata->modulename = 'forum';
        $eventdata->modulefile = 'mod/forum/index.php';
        events_trigger('message_provider_register', $eventdata);
        
        upgrade_mod_savepoint($result, 2008072401, 'forum');
    }

    if ($result && $oldversion < 2008072800) {
    /// Define field completiondiscussions to be added to forum
        $table = new XMLDBTable('forum');
        $field = new XMLDBField('completiondiscussions');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '9', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'draft');

    /// Launch add field completiondiscussions
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

        $field = new XMLDBField('completionreplies');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '9', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'completiondiscussions');

    /// Launch add field completionreplies
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }

    /// Define field completionposts to be added to forum
        $field = new XMLDBField('completionposts');
        $field->setAttributes(XMLDB_TYPE_INTEGER, '9', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'completionreplies');

    /// Launch add field completionposts
        if(!$dbman->field_exists($table,$field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint($result, 2008072800, 'forum');
    }


    return $result;
}

?>
