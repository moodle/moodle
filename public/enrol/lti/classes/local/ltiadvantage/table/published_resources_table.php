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

namespace enrol_lti\local\ltiadvantage\table;

use enrol_lti\helper;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Class which displays a list of resources published over LTI Advantage.
 *
 * @package enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class published_resources_table extends \table_sql {

    /**
     * @var \enrol_plugin $ltiplugin
     */
    protected $ltiplugin;

    /**
     * @var bool $ltienabled
     */
    protected $ltienabled;

    /**
     * @var bool $canconfig
     */
    protected $canconfig;

    /**
     * @var int $courseid The course id.
     */
    protected $courseid;

    /**
     * Sets up the table.
     *
     * @param string $courseid The id of the course.
     */
    public function __construct($courseid) {
        parent::__construct('enrol_lti_manage_table');

        $this->define_columns(array(
            'name',
            'launch',
            'edit'
        ));
        $this->define_headers(array(
            get_string('name'),
            get_string('lti13launchdetails', 'enrol_lti'),
            get_string('edit')
        ));
        $this->collapsible(false);
        $this->sortable(false);

        // Set the variables we need access to.
        $this->ltiplugin = enrol_get_plugin('lti');
        $this->ltienabled = enrol_is_enabled('lti');
        $this->canconfig = has_capability('moodle/course:enrolconfig', \context_course::instance($courseid));
        $this->courseid = $courseid;

        // Set help icons.
        $launchicon = new \help_icon('lti13launchdetails', 'enrol_lti');
        $this->define_help_for_headers(['1' => $launchicon]);
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $tool event data.
     * @return string
     */
    public function col_name($tool) {
        $toolcontext = \context::instance_by_id($tool->contextid, IGNORE_MISSING);
        $name = $toolcontext ? helper::get_name($tool) : $this->get_deleted_activity_name_html($tool);

        return $this->get_display_text($tool, $name);
    }

    /**
     * Generate the launch column.
     *
     * @param \stdClass $tool instance data.
     * @return string
     */
    public function col_launch($tool) {
        global $OUTPUT;

        $customparamslabel = get_string('customproperties', 'enrol_lti');
        $customparams = "id={$tool->uuid}";
        $launchurl = new \moodle_url('/enrol/lti/launch.php');
        $launchurllabel = get_string('launchurl', 'enrol_lti');

        $data = [
                "rows" => [
                    [
                        "label" => $launchurllabel,
                        "text" => $launchurl->out(false),
                        "id" => "launchurl",
                        "hidelabel" => false
                    ],
                    [
                        "label" => $customparamslabel,
                        "text" => $customparams,
                        "id" => "customparams",
                        "hidelabel" => false
                    ]
                ]
            ];

        $return = $OUTPUT->render_from_template("enrol_lti/copy_grid", $data);

        return $return;
    }

    /**
     * Generate the edit column.
     *
     * @param \stdClass $tool event data.
     * @return string
     */
    public function col_edit($tool) {
        global $OUTPUT;

        $buttons = array();

        $instance = new \stdClass();
        $instance->id = $tool->enrolid;
        $instance->courseid = $tool->courseid;
        $instance->enrol = 'lti';
        $instance->status = $tool->status;

        $strdelete = get_string('delete');
        $strenable = get_string('enable');
        $strdisable = get_string('disable');

        $url = new \moodle_url('/enrol/lti/index.php', array('sesskey' => sesskey(), 'courseid' => $this->courseid));

        if ($this->ltiplugin->can_delete_instance($instance)) {
            $aurl = new \moodle_url($url, array('action' => 'delete', 'instanceid' => $instance->id));
            $buttons[] = $OUTPUT->action_icon($aurl, new \pix_icon('t/delete', $strdelete, 'core',
                array('class' => 'iconsmall')));
        }

        if ($this->ltienabled && $this->ltiplugin->can_hide_show_instance($instance)) {
            if ($instance->status == ENROL_INSTANCE_ENABLED) {
                $aurl = new \moodle_url($url, array('action' => 'disable', 'instanceid' => $instance->id));
                $buttons[] = $OUTPUT->action_icon($aurl, new \pix_icon('t/hide', $strdisable, 'core',
                    array('class' => 'iconsmall')));
            } else if ($instance->status == ENROL_INSTANCE_DISABLED) {
                $aurl = new \moodle_url($url, array('action' => 'enable', 'instanceid' => $instance->id));
                $buttons[] = $OUTPUT->action_icon($aurl, new \pix_icon('t/show', $strenable, 'core',
                    array('class' => 'iconsmall')));
            }
        }

        if ($this->ltienabled && $this->canconfig) {
            $linkparams = array(
                'courseid' => $instance->courseid,
                'id' => $instance->id, 'type' => $instance->enrol,
                'returnurl' => new \moodle_url('/enrol/lti/index.php', array('courseid' => $this->courseid))
            );
            $editlink = new \moodle_url("/enrol/editinstance.php", $linkparams);
            $buttons[] = $OUTPUT->action_icon($editlink, new \pix_icon('t/edit', get_string('edit'), 'core',
                array('class' => 'iconsmall')));
        }

        return implode(' ', $buttons);
    }

    /**
     * Query the reader. Store results in the object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $total = helper::count_lti_tools(['courseid' => $this->courseid, 'ltiversion' => 'LTI-1p3']);
        $this->pagesize($pagesize, $total);
        $tools = helper::get_lti_tools(['courseid' => $this->courseid, 'ltiversion' => 'LTI-1p3'],
            $this->get_page_start(), $this->get_page_size());
        $this->rawdata = $tools;
        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    /**
     * Returns text to display in the columns.
     *
     * @param \stdClass $tool the tool
     * @param string $text the text to alter
     * @return string
     */
    protected function get_display_text($tool, $text) {
        if ($tool->status != ENROL_INSTANCE_ENABLED) {
            return \html_writer::tag('div', $text, array('class' => 'dimmed_text'));
        }

        return $text;
    }

    /**
     * Get a warning icon, with tooltip, describing enrolment instances sharing activities which have been deleted.
     *
     * @param \stdClass $tool the tool instance record.
     * @return string the HTML for the name column.
     */
    protected function get_deleted_activity_name_html(\stdClass $tool): string {
        global $OUTPUT;
        $icon = \html_writer::tag(
            'a',
            $OUTPUT->pix_icon('enrolinstancewarning', get_string('deletedactivityalt' , 'enrol_lti'), 'enrol_lti'), [
                "class" => "btn btn-link p-0",
                "role" => "button",
                "data-bs-container" => "body",
                "data-bs-toggle" => "popover",
                "data-bs-placement" => right_to_left() ? "left" : "right",
                "data-bs-content" => get_string('deletedactivitydescription', 'enrol_lti'),
                "data-bs-html" => "true",
                "tabindex" => "0",
                "data-bs-trigger" => "focus",
            ]
        );
        $name = \html_writer::span($icon . get_string('deletedactivity', 'enrol_lti'));
        if ($tool->name) {
            $name .= \html_writer::empty_tag('br') . \html_writer::empty_tag('br') . $tool->name;
        }

        return $name;
    }
}
