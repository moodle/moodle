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
 * mod_dataform submission updated event.
 *
 * @package    mod_dataform
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\admin;

defined('MOODLE_INTERNAL') || die();

/**
 * mod_dataform admin tool class.
 *
 * @package    mod_dataform
 * @copyright  2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class patternvalidator {

    /**
     * Return localised  name.
     *
     * @return string
     */
    public static function get_visible_name() {
        return 'Patterns Validator';
    }

    /**
     * Return localised  description.
     *
     * @return string
     */
    public static function get_description() {
        return 'Patterns Validator';
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public static function get_url() {
        return new \moodle_url('/mod/dataform/admin/admintools.php', array('tool' => 'patternvalidator'));
    }

    /**
     * Run the tool.
     *
     * @return view
     */
    public static function run() {
        global $DB, $OUTPUT;

        $summary = optional_param('summary', 0, PARAM_INT);
        $analyse = optional_param('analyse', 0, PARAM_INT);
        $confirm = optional_param('confirm', 0, PARAM_INT);
        $execute = optional_param('execute', 0, PARAM_INT);

        $baseurl = self::get_url();
        $summarylink = \html_writer::link(new \moodle_url($baseurl, array('summary' => 1)), 'Summary');
        $analyselink = \html_writer::link(new \moodle_url($baseurl, array('analyse' => 1)), 'Analyse');
        $executelink = \html_writer::link(new \moodle_url($baseurl, array('execute' => 1)), 'Execute');

        echo \html_writer::tag('div', "$summarylink | $analyselink | $executelink");

        if (!$dataforms = $DB->get_records('dataform')) {
            return get_string('dataformnone', 'dataform');
        }

        if (!$analyse and !$execute) {
            return self::get_summary($dataforms);
        }

        $brokenpatterns = array();
        foreach ($dataforms as $dataformid => $dataform) {
            $df = \mod_dataform_dataform::instance($dataformid);
            if (!$views = $df->view_manager->get_views()) {
                continue;
            }

            foreach ($views as $viewid => $view) {
                if ($updates = $view->patterns_check()) {
                    $brokenpatterns = array_merge($brokenpatterns, $updates);
                }
            }
        }

        // No problems.
        if (!$brokenpatterns) {
            return $OUTPUT->notification(get_string('patternsnonebroken', 'dataform'), 'notifysuccess');
        }

        $table = new \html_table();
        $table->head = array('Pattern Name', 'Dataform', 'View', 'Type', 'Problem');
        foreach ($brokenpatterns as $info) {
            $table->data[] = array(
                $info['pattern'],
                $info['dataform'],
                $info['view'],
                $info['type'],
                $info['problem'],
            );
        }

        return \html_writer::table($table);
    }

    /**
     * Returns summary of dataforms in the course/site.
     *
     * @params recordset List of dataform instances
     * @return string HTML fragment
     */
    public static function get_summary($dataforms) {
        global $DB;

        $table = new \html_table();
        $table->head = array('Name', 'Views', 'Fields', 'Filters', 'Entries');
        foreach ($dataforms as $dataformid => $instance) {

            $numviews = $DB->count_records('dataform_views', array('dataid' => $dataformid));
            $numfields = $DB->count_records('dataform_fields', array('dataid' => $dataformid));
            $numfilters = $DB->count_records('dataform_filters', array('dataid' => $dataformid));
            $numentries = $DB->count_records('dataform_entries', array('dataid' => $dataformid));

            $table->data[] = array(
                $instance->name,
                $numviews,
                $numfields,
                $numfilters,
                $numentries,
            );
        }

        return \html_writer::table($table);
    }
}
