<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
* This plugin provides access to Moodle data in form of analytics and reports in real time.
*
*
* @package    local_intelliboard
* @copyright  2017 IntelliBoard, Inc
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
* @website    http://intelliboard.net/
*/

function xmldb_local_intelliboard_upgrade($oldversion) {
	global $DB;

	$dbman = $DB->get_manager();

	if ($oldversion < 2015020900) {
		// Define table local_intelliboard_tracking to be created.
		$table = new xmldb_table('local_intelliboard_tracking');
		// Adding fields to table local_intelliboard_tracking.
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('page', XMLDB_TYPE_CHAR, '100', null, null, null, null);
		$table->add_field('param', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('visits', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('timespend', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('firstaccess', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('lastaccess', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('useragent', XMLDB_TYPE_CHAR, '100', null, null, null, null);
		$table->add_field('useros', XMLDB_TYPE_CHAR, '100', null, null, null, null);
		$table->add_field('userlang', XMLDB_TYPE_CHAR, '100', null, null, null, null);
		$table->add_field('userip', XMLDB_TYPE_CHAR, '100', null, null, null, null);

		// Adding keys to table local_intelliboard_tracking.
		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

		// Conditionally launch create table for local_intelliboard_tracking.
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}
		upgrade_plugin_savepoint(true, 2015020900, 'local', 'intelliboard');
	}
	if ($oldversion < 2016011300) {
		$table = new xmldb_table('local_intelliboard_totals');
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('sessions', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('courses', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('visits', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('timespend', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('timepoint', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}

		$table = new xmldb_table('local_intelliboard_logs');
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('trackid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('visits', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('timespend', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('timepoint', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}
		upgrade_plugin_savepoint(true, 2016011300, 'local', 'intelliboard');
	}
	if ($oldversion < 2016030700) {
		$table = new xmldb_table('local_intelliboard_tracking');

		$field = new xmldb_field('useragent');
		$field->set_attributes(XMLDB_TYPE_CHAR, '100', null, null, null, null);
		try {
			$dbman->change_field_type($table, $field);
		} catch (moodle_exception $e) {}

		$field = new xmldb_field('useros');
		$field->set_attributes(XMLDB_TYPE_CHAR, '100', null, null, null, null);
		try {
			$dbman->change_field_type($table, $field);
		} catch (moodle_exception $e) {}

		$field = new xmldb_field('userlang');
		$field->set_attributes(XMLDB_TYPE_CHAR, '100', null, null, null, null);
		try {
			$dbman->change_field_type($table, $field);
		} catch (moodle_exception $e) {}

		$field = new xmldb_field('userip');
		$field->set_attributes(XMLDB_TYPE_CHAR, '100', null, null, null, null);
		try {
			$dbman->change_field_type($table, $field);
		} catch (moodle_exception $e) {}

		upgrade_plugin_savepoint(true, 2016030700, 'local', 'intelliboard');
	}

	if ($oldversion < 2016090900) {
		// Add index to local_intelliboard_tracking
		$table = new xmldb_table('local_intelliboard_tracking');
		$index = new xmldb_index('userid_page_param_idx', XMLDB_INDEX_NOTUNIQUE, array('userid', 'page', 'param'));
		if (!$dbman->index_exists($table, $index)) {
			$dbman->add_index($table, $index);
		}
		// Add index to local_intelliboard_logs
		$table = new xmldb_table('local_intelliboard_logs');
		$index = new xmldb_index('trackid_timepoint_idx', XMLDB_INDEX_NOTUNIQUE, array('trackid', 'timepoint'));
		if (!$dbman->index_exists($table, $index)) {
			$dbman->add_index($table, $index);
		}
		// Add index to local_intelliboard_totals
		$table = new xmldb_table('local_intelliboard_totals');
		$index = new xmldb_index('timepoint_idx', XMLDB_INDEX_NOTUNIQUE, array('timepoint'));
		if (!$dbman->index_exists($table, $index)) {
			$dbman->add_index($table, $index);
		}
		upgrade_plugin_savepoint(true, 2016090900, 'local', 'intelliboard');
	}

	if ($oldversion < 2017072304) {
		$table = new xmldb_table('local_intelliboard_details');
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('logid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('visits', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('timespend', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('timepoint', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0');
		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}

		// Add index to local_intelliboard_details
		$table = new xmldb_table('local_intelliboard_details');
		$index = new xmldb_index('logid_timepoint_idx', XMLDB_INDEX_NOTUNIQUE, array('logid', 'timepoint'));
		if (!$dbman->index_exists($table, $index)) {
			$dbman->add_index($table, $index);
		}
		upgrade_plugin_savepoint(true, 2017072304, 'local', 'intelliboard');
	}

	if ($oldversion < 2017112801) {
		$table = new xmldb_table('local_intelliboard_assign');
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
		$table->add_field('rel', XMLDB_TYPE_CHAR, '64', null, null, null, null);
		$table->add_field('type', XMLDB_TYPE_CHAR, '64', null, null, null, null);
		$table->add_field('instance', XMLDB_TYPE_CHAR, '100', null, null, null, null);
		$table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}
		// Add index to local_intelliboard_assign
		$index = new xmldb_index('type_instance_idx', XMLDB_INDEX_NOTUNIQUE, array('type', 'instance'));
		if (!$dbman->index_exists($table, $index)) {
			$dbman->add_index($table, $index);
		}
		upgrade_plugin_savepoint(true, 2017112801, 'local', 'intelliboard');
	}
	if ($oldversion < 2018052207) {
	    $data = [];
	    $table = new xmldb_table('local_intelliboard_assign');
	    if ($dbman->table_exists($table)) {
	      $data = $DB->get_records("local_intelliboard_assign");
	      $dbman->drop_table($table);
	  	}

		$table = new xmldb_table('local_intelliboard_assign');
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
		$table->add_field('rel', XMLDB_TYPE_CHAR, '64', null, null, null, null);
		$table->add_field('type', XMLDB_TYPE_CHAR, '64', null, null, null, null);
		$table->add_field('instance', XMLDB_TYPE_CHAR, '100', null, null, null, null);
		$table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}
		// Add index to local_intelliboard_assign
		$index = new xmldb_index('type_instance_idx', XMLDB_INDEX_NOTUNIQUE, array('type', 'instance'));
		if (!$dbman->index_exists($table, $index)) {
			$dbman->add_index($table, $index);
		}
		if ($data) {
			$DB->insert_records('local_intelliboard_assign', $data);
		}
		upgrade_plugin_savepoint(true, 2018052207, 'local', 'intelliboard');
	  }

    if ($oldversion < 2018060401) {
        $data = [];
        $table = new xmldb_table('local_intelliboard_ntf');

        if ($dbman->table_exists($table)) {
            $data = $DB->get_records("local_intelliboard_ntf");
            $dbman->drop_table($table);
        }

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('externalid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('subject', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('message', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('state', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('attachment', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tags', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Add index to local_intelliboard_ntf
        $index1 = new xmldb_index('type_idx', XMLDB_INDEX_NOTUNIQUE, array('type'));
        $index2 = new xmldb_index('userid_idx', XMLDB_INDEX_NOTUNIQUE, array('userid'));
        $index3 = new xmldb_index('externalid_idx', XMLDB_INDEX_NOTUNIQUE, array('externalid'));

        if (!$dbman->index_exists($table, $index1)) {
            $dbman->add_index($table, $index1);
        }

        if (!$dbman->index_exists($table, $index2)) {
            $dbman->add_index($table, $index2);
        }

        if (!$dbman->index_exists($table, $index3)) {
            $dbman->add_index($table, $index3);
        }

        if ($data) {
            $DB->insert_records('local_intelliboard_ntf', $data);
        }


        $data = [];
        $table = new xmldb_table('local_intelliboard_ntf_pms');

        if ($dbman->table_exists($table)) {
            $data = $DB->get_records("local_intelliboard_ntf_pms");
            $dbman->drop_table($table);
        }

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('notificationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null);
        $table->add_field('value', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Add index to local_intelliboard_ntf_pms
        $index1 = new xmldb_index('notificationid_idx', XMLDB_INDEX_NOTUNIQUE, array('notificationid'));
        $index2 = new xmldb_index('name_value_idx', XMLDB_INDEX_NOTUNIQUE, array('name', 'value'));

        if (!$dbman->index_exists($table, $index1)) {
            $dbman->add_index($table, $index1);
        }

        if (!$dbman->index_exists($table, $index2)) {
            $dbman->add_index($table, $index2);
        }

        if ($data) {
            $DB->insert_records('local_intelliboard_ntf_pms', $data);
        }

        upgrade_plugin_savepoint(true, 2018060401, 'local', 'intelliboard');
    }

    if ($oldversion < 2018060405) {
        $data = [];
        $table = new xmldb_table('local_intelliboard_ntf_hst');

        if ($dbman->table_exists($table)) {
            $data = $DB->get_records("local_intelliboard_ntf_hst");
            $dbman->drop_table($table);
        }

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('notificationname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('notificationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('email', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timesent', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        // Add index to local_intelliboard_ntf
        $index = new xmldb_index('notificationid_idx', XMLDB_INDEX_NOTUNIQUE, array('notificationid'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        if ($data) {
            $DB->insert_records('local_intelliboard_ntf_hst', $data);
        }

        $table = $table = new xmldb_table("local_intelliboard_ntf");
        $field = new xmldb_field('email');
        $field->set_attributes(XMLDB_TYPE_TEXT, null, null, null, null, null);
        try {
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        $field = new xmldb_field('name');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2018060405, 'local', 'intelliboard');
    }

    if ($oldversion < 2018091501) {
        $table = new xmldb_table('local_intelliboard_bbb_meet');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('meetingname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('meetingid', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('internalmeetingid', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('createtime', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('createdate', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('voicebridge', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('dialnumber', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('attendeepw', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('moderatorpw', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('running', XMLDB_TYPE_CHAR, '25', null, null, null, null);
        $table->add_field('duration', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('hasuserjoined', XMLDB_TYPE_CHAR, '25', null, null, null, null);
        $table->add_field('recording', XMLDB_TYPE_CHAR, '25', null, null, null, null);
        $table->add_field('hasbeenforciblyended', XMLDB_TYPE_CHAR, '25', null, null, null, null);
        $table->add_field('starttime', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('endtime', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('participantcount', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('listenercount', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('voiceparticipantcount', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('videocount', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('maxusers', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('moderatorcount', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('bigbluebuttonbnid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('ownerid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);


        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint(true, 2018091501, 'local', 'intelliboard');
    }


    if ($oldversion < 2018091502) {
        $table = new xmldb_table('local_intelliboard_bbb_atten');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('fullname', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('role', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('ispresenter', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('islisteningonly', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('hasjoinedvoice', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('hasvideo', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('meetingid', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('localmeetingid', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('arrivaltime', XMLDB_TYPE_INTEGER, '15', null, null, null, null);
        $table->add_field('departuretime', XMLDB_TYPE_INTEGER, '15', null, null, null, null);


        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_plugin_savepoint(true, 2018091502, 'local', 'intelliboard');
    }

    if ($oldversion < 2018092603) {
    	$table = new xmldb_table('local_intelliboard_reports');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        $table = new xmldb_table('local_intelliboard_reports');
		$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
		$table->add_field('status', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
		$table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
		$table->add_field('sqlcode', XMLDB_TYPE_TEXT, null, null, null, null, null);
		$table->add_field('appid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
		$table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

		$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
		if (!$dbman->table_exists($table)) {
			$dbman->create_table($table);
		}
		upgrade_plugin_savepoint(true, 2018092603, 'local', 'intelliboard');
	}

    if ($oldversion < 2018092606) {
        $table = new xmldb_table('local_intelliboard_ntf');

        $field = new xmldb_field('cc');
        $field->set_attributes(XMLDB_TYPE_TEXT, null, null, null, null, null);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2018092606, 'local', 'intelliboard');
    }

		if ($oldversion < 2018100103) {
			$data = [];
				$table = new xmldb_table('local_intelliboard_assign');
				if ($dbman->table_exists($table)) {
					$data = $DB->get_records("local_intelliboard_assign");
					$dbman->drop_table($table);
				}

			$table = new xmldb_table('local_intelliboard_assign');
			$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
			$table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
			$table->add_field('rel', XMLDB_TYPE_CHAR, '64', null, null, null, null);
			$table->add_field('type', XMLDB_TYPE_CHAR, '64', null, null, null, null);
			$table->add_field('instance', XMLDB_TYPE_CHAR, '100', null, null, null, null);
			$table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

			$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
			if (!$dbman->table_exists($table)) {
				$dbman->create_table($table);
			}
			// Add index to local_intelliboard_assign
			$index = new xmldb_index('type_instance_idx', XMLDB_INDEX_NOTUNIQUE, array('type', 'instance'));
			if (!$dbman->index_exists($table, $index)) {
				$dbman->add_index($table, $index);
			}
			if ($data) {
				$DB->insert_records('local_intelliboard_assign', $data);
			}
			upgrade_plugin_savepoint(true, 2018100103, 'local', 'intelliboard');
    }


		if ($oldversion < 2019050603) {
				$table = $table = new xmldb_table("local_intelliboard_bbb_atten");
				$field = new xmldb_field('fullname');
				$field->set_attributes(XMLDB_TYPE_TEXT, null, null, null, null, null);
				try {
						$dbman->change_field_type($table, $field);
				} catch (moodle_exception $e) {}

        upgrade_plugin_savepoint(true, 2019050603, 'local', 'intelliboard');
    }

    if ($oldversion < 2019051003) {
        $table = new xmldb_table('local_intelliboard_dbconn');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('connection_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2019051003, 'local', 'intelliboard');
    }


		if ($oldversion < 2019051203) {
			$table = new xmldb_table('local_intelliboard_reports');
			$field = new xmldb_field('status');
			$field->set_attributes(XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
			try {
				$dbman->change_field_type($table, $field);
			} catch (moodle_exception $e) {}

			$table = new xmldb_table('local_intelliboard_bbb_meet');
			$field = new xmldb_field('meetingname');
			$field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
			try {
				$dbman->change_field_type($table, $field);
			} catch (moodle_exception $e) {}

			$field = new xmldb_field('createdate');
				$field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
			try {
				$dbman->change_field_type($table, $field);
			} catch (moodle_exception $e) {}

			$field = new xmldb_field('dialnumber');
			$field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
			try {
				$dbman->change_field_type($table, $field);
			} catch (moodle_exception $e) {}

			$field = new xmldb_field('duration');
			$field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
			try {
				$dbman->change_field_type($table, $field);
			} catch (moodle_exception $e) {}

			upgrade_plugin_savepoint(true, 2019051203, 'local', 'intelliboard');
		}

    if ($oldversion < 2019082804) {

        // Define table local_intelliboard_bb_partic to be created.
        $table = new xmldb_table('local_intelliboard_bb_partic');

        // Adding fields to table local_intelliboard_bb_partic.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sessionuid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('useruid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('external_user_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('role', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('display_name', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table local_intelliboard_bb_partic.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_intelliboard_bb_partic.
        $table->add_index('sessionuid', XMLDB_INDEX_NOTUNIQUE, ['sessionuid']);
        $table->add_index('useruid', XMLDB_INDEX_NOTUNIQUE, ['useruid']);
        $table->add_index('external_user_id', XMLDB_INDEX_NOTUNIQUE, ['external_user_id']);

        // Conditionally launch create table for local_intelliboard_bb_partic.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_intelliboard_bb_trck_m to be created.
        $table = new xmldb_table('local_intelliboard_bb_trck_m');

        // Adding fields to table local_intelliboard_bb_trck_m.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sessionuid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('track_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table local_intelliboard_bb_trck_m.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_intelliboard_bb_trck_m.
        $table->add_index('sessionuid', XMLDB_INDEX_UNIQUE, ['sessionuid']);

        // Conditionally launch create table for local_intelliboard_bb_trck_m.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field first_join_time to be added to local_intelliboard_bb_partic.
        $table = new xmldb_table('local_intelliboard_bb_partic');

        $field = new xmldb_field(
            'first_join_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'display_name'
        );
        // Conditionally launch add field first_join_time.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field(
            'last_left_time', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'first_join_time'
        );
        // Conditionally launch add field last_left_time.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field(
            'duration', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'last_left_time'
        );
        // Conditionally launch add field duration.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field(
            'rejoins', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'duration'
        );
        // Conditionally launch add field rejoins.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table local_intelliboard_bb_rec to be created.
        $table = new xmldb_table('local_intelliboard_bb_rec');

        // Adding fields to table local_intelliboard_bb_rec.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('sessionuid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('record_name', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('record_url', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_intelliboard_bb_rec.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_intelliboard_bb_rec.
        $table->add_index('bb_rec_sessionuid', XMLDB_INDEX_NOTUNIQUE, ['sessionuid']);

        // Conditionally launch create table for local_intelliboard_bb_rec.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_intelliboard_att_sync to be created.
        $table = new xmldb_table('local_intelliboard_att_sync');

        // Adding fields to table local_intelliboard_att_sync.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('instance', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('data', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table local_intelliboard_att_sync.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_intelliboard_att_sync.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Intelliboard savepoint reached.
        upgrade_plugin_savepoint(true, 2019082804, 'local', 'intelliboard');
    }

    if ($oldversion < 2020033123) {

        // Define table local_intelliboard_bb_rec to be dropped.
        $table = new xmldb_table('local_intelliboard_bb_rec');

        // Conditionally launch drop table for local_intelliboard_bb_rec.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Intelliboard savepoint reached.
        upgrade_plugin_savepoint(true, 2020033123, 'local', 'intelliboard');
    }

    if ($oldversion < 2020062500) {
        // Define table local_intelliboard_trns_c to be created.
        $table = new xmldb_table('local_intelliboard_trns_c');
        // Adding fields to table local_intelliboard_trns_c.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('useremail', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('firstname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('lastname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('userenrolid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('enrolid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('enroltype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('coursename', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('enroldate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('unenroldate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('completeddate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('gradeitemid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('gradeid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, '100');
        $table->add_field('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, '0');
        $table->add_field('finalgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, '0');
        $table->add_field('formattedgrade', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('rolesids', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('groupsids', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table local_intelliboard_trns_c.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('enrolid', XMLDB_KEY_FOREIGN, ['enrolid'], 'enrol', ['id']);
        $table->add_key('userenrolid', XMLDB_KEY_FOREIGN, ['userenrolid'], 'user_enrolments', ['id']);
        $table->add_key('gradeitemid', XMLDB_KEY_FOREIGN, ['gradeitemid'], 'grade_items', ['id']);
        $table->add_key('gradeid', XMLDB_KEY_FOREIGN, ['gradeid'], 'grade_grades', ['id']);

        // Conditionally launch create table for local_intelliboard_trns_c.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table local_intelliboard_trns_m to be created.
        $table = new xmldb_table('local_intelliboard_trns_m');
        // Adding fields to table local_intelliboard_trns_m.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userenrolid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('moduleid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('modulename', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('moduletype', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('startdate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('completeddate', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('status', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('gradeitemid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('gradeid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('grademax', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, '100');
        $table->add_field('grademin', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, '0');
        $table->add_field('finalgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, '0');
        $table->add_field('formattedgrade', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table local_intelliboard_trns_m.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('userenrolid', XMLDB_KEY_FOREIGN, ['userenrolid'], 'user_enrolments', ['id']);
        $table->add_key('cmid', XMLDB_KEY_FOREIGN, ['cmid'], 'course_modules', ['id']);
        $table->add_key('moduleid', XMLDB_KEY_FOREIGN, ['moduleid'], 'modules', ['id']);
        $table->add_key('gradeitemid', XMLDB_KEY_FOREIGN, ['gradeitemid'], 'grade_items', ['id']);
        $table->add_key('gradeid', XMLDB_KEY_FOREIGN, ['gradeid'], 'grade_grades', ['id']);

        // Conditionally launch create table for local_intelliboard_trns_m.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2020062500, 'local', 'intelliboard');
    }

    if ($oldversion < 2021021204) {

        // Define table local_intelliboard_config to be dropped.
        $table = new xmldb_table('local_intelliboard_config');

        // Conditionally launch drop table for local_intelliboard_config.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Intelliboard savepoint reached.
        upgrade_plugin_savepoint(true, 2021021204, 'local', 'intelliboard');
    }

    if ($oldversion < 2021031504) {
        // Define index courseid (not unique) to be added to local_intelliboard_bbb_meet.
        $table = new xmldb_table('local_intelliboard_bbb_meet');
        $index = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

        // Conditionally launch add index courseid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Intelliboard savepoint reached.
        upgrade_plugin_savepoint(true, 2021031504, 'local', 'intelliboard');
    }

    if ($oldversion < 2021031508) {
        // Define index localmeetingid (not unique) to be added to local_intelliboard_bbb_atten.
        $table = new xmldb_table('local_intelliboard_bbb_atten');
        $index = new xmldb_index('localmeetingid', XMLDB_INDEX_NOTUNIQUE, ['localmeetingid']);

        // Conditionally launch add index localmeetingid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Intelliboard savepoint reached.
        upgrade_plugin_savepoint(true, 2021031508, 'local', 'intelliboard');
    }

    if ($oldversion > 2022021700) {
        $table = new xmldb_table('course');
        $field = new xmldb_field('containertype');
        if ($dbman->field_exists($table, $field)) {
            set_config('coursecontainer_available','1','local_intelliboard');
        } else {
            set_config('coursecontainer_available', '0','local_intelliboard');
        }
    }

    if ($oldversion < 2022051800) {
        // Add index to local_intelliboard_tracking
        $table = new xmldb_table('local_intelliboard_tracking');
        $index = new xmldb_index('userid_courseid_idx', XMLDB_INDEX_NOTUNIQUE, array('userid', 'courseid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_plugin_savepoint(true, 2022051800, 'local', 'intelliboard');
    }

    if ($oldversion < 2022060204) {

        // Update local_intelliboard_trns_c table.
        $table = new xmldb_table("local_intelliboard_trns_c");

        try {
            $field = new xmldb_field('useremail', XMLDB_TYPE_CHAR, '255', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('firstname', XMLDB_TYPE_CHAR, '255', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('lastname', XMLDB_TYPE_CHAR, '255', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('enroltype', XMLDB_TYPE_CHAR, '100', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('coursename', XMLDB_TYPE_CHAR, '255', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('formattedgrade', XMLDB_TYPE_CHAR, '100', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('rolesids', XMLDB_TYPE_CHAR, '100', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('groupsids', XMLDB_TYPE_CHAR, '100', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('finalgrade', XMLDB_TYPE_NUMBER, '10, 5', null, null, null, '0');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        // Update local_intelliboard_trns_m table.
        $table = new xmldb_table("local_intelliboard_trns_m");

        try {
            $field = new xmldb_field('modulename', XMLDB_TYPE_CHAR, '255', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('moduletype', XMLDB_TYPE_CHAR, '100', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('formattedgrade', XMLDB_TYPE_CHAR, '100', null, false, null, '');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        // Update local_intelliboard_details table.
        $table = new xmldb_table("local_intelliboard_details");

        $index = new xmldb_index('logid_timepoint_idx', XMLDB_INDEX_NOTUNIQUE, ['logid', 'timepoint']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        try {
            $field = new xmldb_field('logid', XMLDB_TYPE_INTEGER, '10', null, false, null, '0');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('visits', XMLDB_TYPE_INTEGER, '10', null, false, null, '0');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('timespend', XMLDB_TYPE_INTEGER, '10', null, false, null, '0');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        try {
            $field = new xmldb_field('timepoint', XMLDB_TYPE_INTEGER, '10', null, false, null, '0');
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {}

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2022060204, 'local', 'intelliboard');
    }

    if ($oldversion < 2023092702) {
        $table = new xmldb_table('local_intelliboard_bbb_meet');
        $field = new xmldb_field('meetingname');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        try {
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {
        }

        $field = new xmldb_field('createdate');
            $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        try {
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {
        }

        $field = new xmldb_field('dialnumber');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        try {
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {
        }

        $field = new xmldb_field('duration');
        $field->set_attributes(XMLDB_TYPE_CHAR, '255', null, null, null, null);
        try {
            $dbman->change_field_type($table, $field);
        } catch (moodle_exception $e) {
        }

        upgrade_plugin_savepoint(true, 2023092702, 'local', 'intelliboard');
    }

    return true;
}
