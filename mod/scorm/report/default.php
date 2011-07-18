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
 * @package    mod
 * @subpackage scorm
 * @author     Ankit Kumar Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
////////////////////////////////////////////////////////////////////
/// Default class for Scorm plugins
///
/// Doesn't do anything on it's own -- it needs to be extended.
/// This class displays scorm reports.  Because it is called from
/// within /mod/scorm/report.php you can assume that the page header
/// and footer are taken care of.
///
/// This file can refer to itself as report.php to pass variables
/// to itself - all these will also be globally available.
////////////////////////////////////////////////////////////////////

defined('MOODLE_INTERNAL') || die();

class scorm_default_report {
    /**
     * displays the full report
     * @param stdClass $scorm full SCORM object
     * @param stdClass $cm - full course_module object
     * @param stdClass $course - full course object
     * @param string $download - type of download being requested
     */
    function display($scorm, $cm, $course, $download) {
        /// This function just displays the report
        return true;
    }
    /**
     * allows the plugin to control who can see this plugin.
     * @return boolean
     */
    function canview($contextmodule) {
        return true;
    }
}
