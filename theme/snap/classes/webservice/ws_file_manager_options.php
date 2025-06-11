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

namespace theme_snap\webservice;

use stdClass;
use context_course;
use core_external\external_api;
use core_external\external_value;
use core_external\external_function_parameters;
use core_external\external_single_structure;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/repository/lib.php');

/**
 * File manager options data service
 * @author    Daniel Cifuentes
 * @copyright Copyright (c) 2024 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ws_file_manager_options extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function service_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * @return external_single_structure
     */
    public static function service_returns() {
        return new external_single_structure([
            'fpoptions'    => new external_value(PARAM_TEXT, 'JSON with file manager data')
        ]);
    }

    /**
     * @return array
     */
    public static function service() {

        global $CFG, $COURSE, $PAGE;

        $context = context_course::instance($COURSE->id);
        $PAGE->set_context($context);

        $args = new stdClass();
        $args->accepted_types = array('.jpeg', '.png', '.gif');
        $args->return_types = 2;
        $args->context = $context;
        $args->env = 'filepicker';

        $fpoptions = initialise_filepicker($args);
        $fpoptions->context = $context;
        $fpoptions->client_id = uniqid();
        $fpoptions->areamaxbytes = get_max_upload_file_size($CFG->maxbytes);
        $fpoptions->env = 'filemanager';
        $fpoptions->magicscope = $context;
        $fpoptions->itemid = file_get_unused_draft_itemid();

        $optionsencoded = json_encode($fpoptions);

        $data = array();
        $data['fpoptions'] = $optionsencoded;
        return $data;
    }
}
