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
 * Rule cm.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/lib.php');

/**
 * Rule cm class.
 *
 * Option to filter by course module.
 *
 * @package    block_xp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_xp_rule_cm extends block_xp_rule_property {

    /** @var config The configuration. */
    protected $config;

    /**
     * Course ID used when we populate the form.
     * @var int
     */
    protected $courseid;

    /**
     * The class property to compare against.
     *
     * @var string
     */
    protected $property;

    /**
     * Constructor.
     *
     * @param int $courseid The course ID.
     * @param int $contextid The context ID.
     */
    public function __construct($courseid = 0, $contextid = 0) {
        global $COURSE;
        $this->courseid = empty($courseid) ? $COURSE->id : $courseid;
        $this->config = \block_xp\di::get('config');
        parent::__construct(self::EQ, $contextid, 'contextid');
    }

    /**
     * Returns a string describing the rule.
     *
     * @return string
     */
    public function get_description() {
        return $this->get_display_name();
    }

    /**
     * Get display name.
     *
     * @return string
     */
    protected function get_display_name() {
        if (empty($this->value)) {
            return get_string('errorunknownmodule', 'block_xp');
        }
        $context = context::instance_by_id($this->value, IGNORE_MISSING);
        if (!$context) {
            return get_string('errorunknownmodule', 'block_xp');
        }

        $str = 'rulecmdesc';
        $strparams = ['contextname' => $context->get_context_name(false)];
        $coursecontext = false;
        if ($this->config->get('context') == CONTEXT_SYSTEM) {
            $coursecontext = $context->get_course_context(false);
        }

        if (!empty($coursecontext)) {
            $str = 'rulecmdescwithcourse';
            $strparams['coursename'] = $coursecontext->get_context_name(false, true);
        }

        return get_string($str, 'block_xp', (object) $strparams);
    }

    /**
     * Returns a form element for this rule.
     *
     * @param string $basename The form element base name.
     * @return string
     */
    public function get_form($basename) {
        if ($this->config->get('context') == CONTEXT_SYSTEM) {
            return $this->get_advanced_form($basename);
        }
        return $this->get_simple_form($basename);
    }

    /**
     * Get advanced form.
     *
     * This is used when we're using one block for the whole site,
     * we can't display all modules at once as there would be too many.
     *
     * @param string $basename The base name.
     * @return string
     */
    protected function get_advanced_form($basename) {
        $output = \block_xp\di::get('renderer');
        $hascm = !empty($this->value);

        $o = block_xp_rule::get_form($basename);
        $o .= html_writer::start_tag('span', ['class' => 'block_xp-cm-rule-widget ' . ($hascm ? 'has-cm' : null)]);
        $o .= html_writer::empty_tag('input', [
            'name' => $basename . '[value]',
            'class' => 'cm-rule-contextid',
            'type' => 'hidden',
            'value' => $this->value,
        ]);

        if (!$hascm) {
            // We can only select the CM once!
            static::init_page_requirements();
            $o .= html_writer::start_tag('span', ['class' => 'cm-selection']);
            $o .= html_writer::tag('button', get_string('clicktoselectcm', 'block_xp'), ['class' => 'btn btn-warning']);
            $o .= html_writer::end_tag('span');
        }

        $o .= html_writer::start_tag('span', ['class' => 'cm-selected']);
        $o .= $this->get_display_name();
        $o .= html_writer::end_tag('span');

        $o .= $output->help_icon('rulecm', 'block_xp');
        $o .= html_writer::end_tag('span');
        return $o;
    }

    /**
     * Get simple form.
     *
     * This is used when we're using one block per course, and as such
     * can display all modules in a select box.
     *
     * @param string $basename The base name.
     * @return string
     */
    protected function get_simple_form($basename) {
        global $COURSE;
        $output = \block_xp\di::get('renderer');
        $options = [];

        $valuefound = empty($this->value);
        $modinfo = get_fast_modinfo($this->courseid);
        $courseformat = course_get_format($this->courseid);

        foreach ($modinfo->get_sections() as $sectionnum => $cmids) {
            $modules = [];
            foreach ($cmids as $cmid) {
                $cm = $modinfo->get_cm($cmid);
                if (!empty($cm->deletioninprogress)) {
                    continue;
                }
                $modules[$cm->context->id] = $cm->name;
                $valuefound = $valuefound || $this->value == $cm->context->id;
            }
            $options[] = [$courseformat->get_section_name($sectionnum) => $modules];
        }

        if (!$valuefound) {
            $options[] = [get_string('error') => [$this->value => get_string('errorunknownmodule', 'block_xp')]];
        }

        $o = block_xp_rule::get_form($basename);
        $modules = html_writer::select($options, $basename . '[value]', $this->value, '', ['id' => '', 'class' => '']);
        $helpicon = $output->help_icon('rulecm', 'block_xp');

        $o .= html_writer::start_div('xp-flex xp-gap-1 xp-min-full');
        $o .= html_writer::start_div('xp-flex xp-items-center');
        $o .= get_string('activityoresourceis', 'block_xp', '');
        $o .= html_writer::end_div();
        $o .= html_writer::div($modules . $helpicon, 'xp-flex xp-items-center xp-min-w-px xp-whitespace-nowrap');
        $o .= html_writer::end_div();

        return $o;
    }

    /**
     * Update the rule after a restore.
     *
     * @param string $restoreid The restore ID.
     * @param int $courseid The course ID.
     * @param base_logger $logger The logger.
     * @return void
     */
    public function update_after_restore($restoreid, $courseid, base_logger $logger) {
        if (!empty($this->value)) {
            $newid = restore_dbops::get_backup_ids_record($restoreid, 'context', $this->value);
            if (!$newid || !$newid->newitemid) {
                $logger->process("Could not find mapping for context {$this->value}", backup::LOG_WARNING);
                return;
            }
            $this->value = (int) $newid->newitemid;
        }
    }

    /**
     * Initialise the page requirements.
     *
     * @return void
     */
    protected static function init_page_requirements() {
        global $PAGE, $COURSE; // @codingStandardsIgnoreLine

        static $alreadydone = false;
        if ($alreadydone) {
            return;
        }
        $alreadydone = true;

        $args = [];

        // This currently has no effect as when we use one block per course, the page always has the system context.
        if ($COURSE->id != SITEID) {
            $args = array_intersect_key((array) $COURSE, array_flip(['id', 'fullname', 'displayname', 'shortname', 'categoryid']));
        }

        // @codingStandardsIgnoreStart
        $PAGE->requires->js_call_amd('block_xp/cm-rule', 'init', $args);
        $PAGE->requires->strings_for_js(['cmselector', 'rulecmdescwithcourse'], 'block_xp');
        // @codingStandardsIgnoreEnd
    }

}
