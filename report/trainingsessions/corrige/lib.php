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
 * This file contains functions used by the trainingsessions report
 *
 * @package    report_trainingsessions
 * @category   report
 * @copyright  2012 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function report_trainingsessions_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('report/trainingsessions:view', $context)) {
        $url = new moodle_url('/report/trainingsessions/index.php', array('id' => $course->id));
        $label = get_string('pluginname', 'report_trainingsessions');
        $navigation->add($label, $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }
}

function report_trainingsessions_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $array = array(
        '*' => get_string('page-x', 'pagetype'),
        'report-*' => get_string('page-report-x', 'pagetype'),
        'report-trainingsessions-*' => get_string('page-report-trainingsessions-x',  'report_trainingsessions'),
        'report-trainingsessions-index' => get_string('page-report-trainingsessions-index',  'report_trainingsessions'),
    );
    return $array;
}

/**
 * Is current user allowed to access this report
 *
 * @private defined in lib.php for performance reasons
 *
 * @param stdClass $user
 * @param stdClass $course
 * @return bool
 */
function report_trainingsessions_can_access_user_report($user, $course) {
    global $USER;

    $coursecontext = context_course::instance($course->id);

    if (has_capability('report/trainingsessions:view', $coursecontext)) {
        return true;
    } else if ($user->id == $USER->id) {
        if ($course->showreports and (is_viewing($coursecontext, $USER) or is_enrolled($coursecontext, $USER))) {
            return true;
        }
    }

    return false;
}

/**
 * Called by the storage subsystem to give back a raw report
 *
 */
function report_trainingsessions_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $USER;

    if ($USER->id) {
        require_capability('report/trainingsessions:downloadreports', $context);
    }

    if (!in_array($filearea, array('rawreports', 'reports'))) {
        send_file_not_found();
    }

    $fs = get_file_storage();

    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/'.implode('/', $args).'/' : '/';
    if ((!$file = $fs->get_file($context->id, 'report_trainingsessions', $filearea, $itemid, $filepath, $filename)) ||
            $file->is_directory()) {
        send_file_not_found();
    }

    send_stored_file($file, 60 * 60, 0, true);
}

/**
 * Tells wether a feature is supported or not. Gives back the
 * implementation path where to fetch resources.
 * @param string $feature a feature key to be tested.
 */
function report_trainingsessions_supports_feature($feature) {
    global $CFG;
    static $supports;

    $config = get_config('report_trainingsessions');

    if (!isset($supports)) {
        $supports = array(
            'pro' => array(
                'format' => array('xls', 'csv', 'pdf', 'json'),
                'replay' => array('single', 'replay', 'shift', 'shiftto'),
                'calculation' => array('coupling', 'specialgrades'),
                'xls' => array('calculated'),
                'export' => array('ws')
            ),
            'community' => array(
                'format' => array('xls', 'csv'),
                'replay' => array('single', 'replay'),
            ),
        );
        $prefer = array('format' => array(
            'xls' => 'community',
            'csv' => 'community'
        ));
    }

    // Check existance of the 'pro' dir in plugin.
    if (is_dir(__DIR__.'/pro')) {
        if ($feature == 'emulate/community') {
            return 'pro';
        }
        if (empty($config->emulatecommunity)) {
            $versionkey = 'pro';
        } else {
            $versionkey = 'community';
        }
    } else {
        $versionkey = 'community';
    }

    list($feat, $subfeat) = explode('/', $feature);

    if (!array_key_exists($feat, $supports[$versionkey])) {
        return false;
    }

    if (!in_array($subfeat, $supports[$versionkey][$feat])) {
        return false;
    }

    // Special condition for pdf dependencies.
    if (($feature == 'format/pdf') && !is_dir($CFG->dirroot.'/local/vflibs')) {
        return false;
    }

    if (array_key_exists($feat, $supports['community'])) {
        if (in_array($subfeat, $supports['community'][$feat])) {
            // If community exists, default path points community code.
            if (isset($prefer[$feat][$subfeat])) {
                // Configuration tells which location to prefer if explicit.
                $versionkey = $prefer[$feat][$subfeat];
            } else {
                $versionkey = 'community';
            }
        }
    }

    return $versionkey;
}

/**
 * Callback to verify if the given instance of store is supported by this report or not.
 *
 * @param string $instance store instance.
 *
 * @return bool returns true if the store is supported by the report, false otherwise.
 */
function report_trainingsessions_supports_feature_logstore($instance) {
    if ($instance instanceof \core\log\sql_internal_reader || $instance instanceof \logstore_legacy\log\store) {
        return true;
    }
    return false;
}

/**
 * Legacy cron function.
 */
function report_trainingsessions_cron() {
    assert(1);
}
