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
    /** @var string|null Indicate whether we are managing template or not. */
    private $mode;

    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     * @param moodle_url $baseurl
     * @param string $mode Indicate whether we are managing templates
     */
    public function __construct($uniqueid, $baseurl, ?string $mode = null) {
        parent::__construct($uniqueid);
        $this->mode = $mode;
        $tablecolumns = array('template');
        if ($this->mode) {
            $tablecolumns[] = 'actions';
        }

        $tableheaders = array(get_string('template', 'feedback'), '');

        $this->set_attribute('class', 'templateslist');

        $this->define_columns($tablecolumns);
        $this->define_headers($tableheaders);
        $this->define_baseurl($baseurl);
        $this->column_class('template', 'template');
        $this->column_class('actions', 'text-end');
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
            $data = [];
            $url = new moodle_url($this->baseurl, array('templateid' => $template->id, 'sesskey' => sesskey()));
            $data[] = $OUTPUT->action_link($url, format_string($template->name));

            // Only show the actions if we are managing templates.
            if ($this->mode && has_capability('mod/feedback:deletetemplate', $this->get_context())) {
                $deleteurl = new moodle_url('/mod/feedback/manage_templates.php',
                    $url->params() + ['deletetemplate' => $template->id]);
                $deleteaction = new confirm_action(get_string('confirmdeletetemplate', 'feedback'));
                $deleteicon = $OUTPUT->action_icon($deleteurl, new pix_icon('t/delete', $strdeletefeedback), $deleteaction);
                if ($template->ispublic) {
                    $systemcontext = context_system::instance();
                    if (!(has_capability('mod/feedback:createpublictemplate', $systemcontext) &&
                        has_capability('mod/feedback:deletetemplate', $systemcontext))) {
                        $deleteicon = false;
                    }
                }
                $data[] = $deleteicon;
            }

            $this->add_data($data);
        }
        $this->finish_output();
    }
}
