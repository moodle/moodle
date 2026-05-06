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
 * Search setup steps info.
 *
 * @package core
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_searchsetupinfo extends admin_setting {

    /**
     * Calls parent::__construct with specific arguments
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('searchsetupinfo', '', '', '');
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true, does nothing
     *
     * @return true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Always returns '', does not write anything
     *
     * @param array $data
     * @return string Always returns ''
     */
    public function write_setting($data) {
        // Do not write any setting.
        return '';
    }

    /**
     * Builds the HTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT, $ADMIN;

        $return = '';
        $brtag = html_writer::empty_tag('br');

        $searchareas = \core_search\manager::get_search_areas_list();
        $anyenabled = !empty(\core_search\manager::get_search_areas_list(true));
        $anyindexed = false;
        foreach ($searchareas as $areaid => $searcharea) {
            list($componentname, $varname) = $searcharea->get_config_var_name();
            if (get_config($componentname, $varname . '_indexingstart')) {
                $anyindexed = true;
                break;
            }
        }

        $return .= $OUTPUT->heading(get_string('searchsetupinfo', 'admin'), 3, 'main');

        $table = new html_table();
        $table->head = array(get_string('step', 'search'), get_string('status'));
        $table->colclasses = array('leftalign step', 'leftalign status');
        $table->id = 'searchsetup';
        $table->attributes['class'] = 'admintable table generaltable table-hover';
        $table->data = array();

        $return .= $brtag . get_string('searchsetupdescription', 'search') . $brtag . $brtag;

        // Select a search engine.
        $row = array();
        $url = new moodle_url('/admin/settings.php?section=manageglobalsearch#admin-searchengine');
        $row[0] = '1. ' . html_writer::tag('a', get_string('selectsearchengine', 'admin'),
                        array('href' => $url));

        $status = html_writer::tag('span', get_string('no'), array('class' => 'badge bg-danger text-white'));
        if (!empty($CFG->searchengine)) {
            $status = html_writer::tag('span', get_string('pluginname', 'search_' . $CFG->searchengine),
                array('class' => 'badge bg-success text-white'));

        }
        $row[1] = $status;
        $table->data[] = $row;

        // Available areas.
        $row = array();
        $url = new moodle_url('/admin/searchareas.php');
        $row[0] = '2. ' . html_writer::tag('a', get_string('enablesearchareas', 'admin'),
                        array('href' => $url));

        $status = html_writer::tag('span', get_string('no'), array('class' => 'badge bg-danger text-white'));
        if ($anyenabled) {
            $status = html_writer::tag('span', get_string('yes'), array('class' => 'badge bg-success text-white'));

        }
        $row[1] = $status;
        $table->data[] = $row;

        // Setup search engine.
        $row = array();
        if (empty($CFG->searchengine)) {
            $row[0] = '3. ' . get_string('setupsearchengine', 'admin');
            $row[1] = html_writer::tag('span', get_string('no'), array('class' => 'badge bg-danger text-white'));
        } else {
            if ($ADMIN->locate('search' . $CFG->searchengine)) {
                $url = new moodle_url('/admin/settings.php?section=search' . $CFG->searchengine);
                $row[0] = '3. ' . html_writer::link($url, get_string('setupsearchengine', 'core_admin'));
            } else {
                $row[0] = '3. ' . get_string('setupsearchengine', 'core_admin');
            }

            // Check the engine status.
            $searchengine = \core_search\manager::search_engine_instance();
            try {
                $serverstatus = $searchengine->is_server_ready();
            } catch (\moodle_exception $e) {
                $serverstatus = $e->getMessage();
            }
            if ($serverstatus === true) {
                $status = html_writer::tag('span', get_string('yes'), array('class' => 'badge bg-success text-white'));
            } else {
                $status = html_writer::tag('span', $serverstatus, array('class' => 'badge bg-danger text-white'));
            }
            $row[1] = $status;
        }
        $table->data[] = $row;

        // Indexed data.
        $row = array();
        $url = new moodle_url('/admin/searchareas.php');
        $row[0] = '4. ' . html_writer::tag('a', get_string('indexdata', 'admin'), array('href' => $url));
        if ($anyindexed) {
            $status = html_writer::tag('span', get_string('yes'), array('class' => 'badge bg-success text-white'));
        } else {
            $status = html_writer::tag('span', get_string('no'), array('class' => 'badge bg-danger text-white'));
        }
        $row[1] = $status;
        $table->data[] = $row;

        // Enable global search.
        $row = array();
        $url = new moodle_url("/admin/search.php?query=enableglobalsearch");
        $row[0] = '5. ' . html_writer::tag('a', get_string('enableglobalsearch', 'admin'),
                        array('href' => $url));
        $status = html_writer::tag('span', get_string('no'), array('class' => 'badge bg-danger text-white'));
        if (\core_search\manager::is_global_search_enabled()) {
            $status = html_writer::tag('span', get_string('yes'), array('class' => 'badge bg-success text-white'));
        }
        $row[1] = $status;
        $table->data[] = $row;

        // Replace front page search.
        $row = array();
        $url = new moodle_url("/admin/search.php?query=searchincludeallcourses");
        $row[0] = '6. ' . html_writer::tag('a', get_string('replacefrontsearch', 'admin'),
                                           array('href' => $url));
        $status = html_writer::tag('span', get_string('no'), array('class' => 'badge bg-danger text-white'));
        if (\core_search\manager::can_replace_course_search()) {
            $status = html_writer::tag('span', get_string('yes'), array('class' => 'badge bg-success text-white'));
        }
        $row[1] = $status;
        $table->data[] = $row;

        $return .= html_writer::table($table);

        return highlight($query, $return);
    }

}
