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
 * Base lib class for singleview functionality.
 *
 * @package   gradereport_singleview
 * @copyright 2014 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->dirroot . '/grade/report/singleview/classes/lib.php');

class gradereport_singleview extends grade_report {

    public static function valid_screens() {
        $screendir = dirname(__FILE__) . '/screens';

        $is_valid = function($filename) use ($screendir) {
            if (preg_match('/^\./', $filename)){
                return false;
            }

            $file = $screendir . '/' . $filename;

            if (is_file($file)) {
                return false;
            }

            $plugin = $file . '/lib.php';
            return file_exists($plugin);
        };

        return array_filter(scandir($screendir), $is_valid);
    }

    public static function classname($screen) {
        $screendir = dirname(__FILE__) . '/screens/' . $screen;

        require_once $screendir . '/lib.php';

        return 'gradereport_singleview_' . $screen;
    }

    public static function filters() {
        $classnames = array('gradereport_singleview', 'classname');
        $classes = array_map($classnames, self::valid_screens());

        $screens = array_filter($classes, function($screen) {
            return method_exists($screen, 'filter');
        });

        return function($item) use ($screens) {
            $reduced = function($in, $screen) use ($item) {
                return $in && $screen::filter($item);
            };

            return array_reduce($screens, $reduced, true);
        };
    }

    function process_data($data) {
        if (has_capability('moodle/grade:manage', $this->context)) {
            return $this->screen->process($data);
        }
    }

    function process_action($target, $action) {
    }

    function _s($key, $a = null) {
        return get_string($key, 'gradereport_singleview', $a);
    }

    function __construct($courseid, $gpr, $context, $itemtype, $itemid, $groupid=null) {
        parent::__construct($courseid, $gpr, $context);

        $class = self::classname($itemtype);

        $this->screen = new $class($courseid, $itemid, $groupid);

        // Load custom or predifined js
        $this->screen->js();

        $base = '/grade/report/singleview/index.php';

        $id_params = array('id' => $courseid);

        $this->baseurl = new moodle_url($base, $id_params);

        $this->pbarurl = new moodle_url($base, $id_params + array(
            'item' => $itemtype,
            'itemid' => $itemid
        ));

        $this->setup_groups();
    }

    function output() {
        global $OUTPUT;
        return $OUTPUT->box($this->screen->html());
    }
}

function gradereport_singleview_profilereport($course, $user) {
    global $CFG, $OUTPUT;

    if (!function_exists('gradereport_user_profilereport')) {
        require_once $CFG->dirroot . '/grade/report/user/lib.php';
    }

    $context = context_course::instance($course->id);

    $can_use = (
        has_capability('gradereport/singleview:view', $context) and
        has_capability('moodle/grade:viewall', $context) and
        has_capability('moodle/grade:edit', $context)
    );

    if (!$can_use) {
        gradereport_user_profilereport($course, $user);
    } else {
        $gpr = new grade_plugin_return(array(
            'type' => 'report',
            'plugin' => 'singleview',
            'courseid' => $course->id,
            'userid' => $user->id
        ));

        $report = new gradereport_singleview($course->id, $gpr, $context, 'user', $user->id);

        echo $OUTPUT->heading($report->screen->heading());
        echo $report->output();
    }
}
