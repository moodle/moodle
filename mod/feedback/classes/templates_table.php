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
 * Contains class mod_feedback_templates_table
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Class mod_feedback_templates_table
 *
 * @package   mod_feedback
 * @copyright 2016 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_feedback_templates_table extends flexible_table {

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     * @param moodle_url $baseurl
     */
    public function __construct($uniqueid, $baseurl) {
        parent::__construct($uniqueid);

        $tablecolumns = array('template', 'action');
        $tableheaders = array(get_string('template', 'feedback'), '');

        $this->set_attribute('class', 'templateslist');

        $this->define_columns($tablecolumns);
        $this->define_headers($tableheaders);
        $this->define_baseurl($baseurl);
        $this->column_class('template', 'template');
        $this->column_class('action', 'action');

        $this->sortable(false);
    }

    /**
     * Displays the table with the given set of templates
     * @param array $templates
     */
    public function display($templates) {
        global $OUTPUT;
        if (empty($templates)) {
            echo $OUTPUT->box(get_string('no_templates_available_yet', 'feedback'),
                             'generalbox boxaligncenter');
            return;
        }

        $this->setup();
        $strdeletefeedback = get_string('delete_template', 'feedback');

        foreach ($templates as $template) {
            $data = array();
            $data[] = format_string($template->name);
            $url = new moodle_url($this->baseurl, array('deletetempl' => $template->id, 'sesskey' => sesskey()));

            $deleteaction = new confirm_action(get_string('confirmdeletetemplate', 'feedback'));
            $data[] = $OUTPUT->action_icon($url, new pix_icon('t/delete', $strdeletefeedback), $deleteaction);
            $this->add_data($data);
        }
        $this->finish_output();
    }
}
