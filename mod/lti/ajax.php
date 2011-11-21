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
 * AJAX service used when adding an External Tool to provide immediate feedback
 * of which tool provider is to be used based on the Launch URL.
 *
 * @package    mod
 * @subpackage xml
 * @copyright Copyright (c) 2011 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Chris Scribner
 */

require_once(dirname(__FILE__) . "/../../config.php");
require_once($CFG->dirroot . '/mod/lti/locallib.php');

$courseid = required_param('course', PARAM_INT);

require_login($courseid, false);

$action = required_param('action', PARAM_TEXT);

$response = new stdClass();

switch ($action) {
    case 'find_tool_config':
        $toolurl = required_param('toolurl', PARAM_RAW);
        $toolid = optional_param('toolid', 0, PARAM_INT);

        if(empty($toolid) && !empty($toolurl)){
            $tool = lti_get_tool_by_url_match($toolurl, $courseid);

            if(!empty($tool)){
                $toolid = $tool->id;

                $response->toolid = $tool->id;
                $response->toolname = htmlspecialchars($tool->name);
                $response->tooldomain = htmlspecialchars($tool->tooldomain);
            }
        } else {
            $response->toolid = $toolid;
        }

        if (!empty($toolid)) {
            // Look up privacy settings
            $query = '
                SELECT name, value
                FROM {lti_types_config}
                WHERE
                    typeid = :typeid
                AND name IN (\'sendname\', \'sendemailaddr\', \'acceptgrades\')
            ';

            $privacyconfigs = $DB->get_records_sql($query, array('typeid' => $toolid));
            foreach($privacyconfigs as $config){
                $configname = $config->name;
                $response->$configname = $config->value;
            }
        }
        break;
}

echo json_encode($response);

die;
