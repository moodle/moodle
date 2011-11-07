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
 * MRTODO: Brief description of this file
 *
 * @package    mod
 * @subpackage xml
 * @copyright  2011 onwards MRTODO
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . "/../../config.php");
require_once($CFG->dirroot . '/mod/lti/locallib.php');

$courseid = required_param('course', PARAM_INT);

require_login($courseid, false);

$action = required_param('action', PARAM_TEXT);

$response = new stdClass();

switch($action){
    case 'find_tool_config':
        $toolurl = required_param('toolurl', PARAM_RAW);
        
        $tool = lti_get_tool_by_url_match($toolurl, $courseid);
        
        if(!empty($tool)){
            $response->toolid = $tool->id;
            $response->toolname = htmlspecialchars($tool->name);
            $response->tooldomain = htmlspecialchars($tool->tooldomain);
        }
        
        break;
}

echo json_encode($response);

die;
