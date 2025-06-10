<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
defined("MOODLE_INTERNAL") || die();
function xmldb_respondusws_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2013061700) {
        $table = new xmldb_table("respondusws_auth_users");
        $table->add_field("id", XMLDB_TYPE_INTEGER, "10", XMLDB_UNSIGNED,
          XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null);
        $table->add_field("responduswsid", XMLDB_TYPE_INTEGER, "10",
          XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, "id");
        $table->add_field("userid", XMLDB_TYPE_INTEGER, "10", XMLDB_UNSIGNED,
          XMLDB_NOTNULL, null, null, "responduswsid");
        $table->add_field("authtoken", XMLDB_TYPE_CHAR, "64", null,
          XMLDB_NOTNULL, null, null, "userid");
        $table->add_field("timeissued", XMLDB_TYPE_INTEGER, "10",
          XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, "authtoken");
        $table->add_key("primary", XMLDB_KEY_PRIMARY, array("id"));
        $table->add_key("responduswsid_fk", XMLDB_KEY_FOREIGN,
          array("responduswsid"), "respondusws", array("id"));
        $table->add_key("userid_fk", XMLDB_KEY_FOREIGN_UNIQUE,
          array("userid"), "user", array("id"));
        $table->add_index("authtoken_ix", XMLDB_INDEX_UNIQUE,
          array("authtoken"));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2013061700, "respondusws");
    }
    return true;
}
