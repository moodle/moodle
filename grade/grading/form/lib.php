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
 * @package    core
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Grading method controller encapsulates the logic of the plugin
 *
 * @copyright 2011 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class gradingform_controller {

    /** @var stdClass the context */
    protected $context;

    /** @var string the frankenstyle name of the component */
    protected $component;

    /** @var string the name of the gradable area */
    protected $area;

    /** @var int the id of the gradable area record */
    protected $areaid;

    /** @var moodle_page the target page we embed our widgets to */
    protected $page;

    /** @var stdClass|false the raw {grading_definitions} record */
    protected $definition;

    /** @var bool is the target grading page finalized for sending output to the browser */
    protected $pagefinalized = false;

    /**
     * Do not instantinate this directly, use {@link grading_manager::get_controller()}
     *
     * @return gradingform_controller instance
     */
    public function __construct(stdClass $context, $component, $area, $areaid) {
        global $DB;

        $this->context      = $context;
        $this->component    = $component;
        $this->area         = $area;
        $this->areaid       = $areaid;

        $this->load_definition();
    }

    /**
     * Is the grading form defined and released for usage?
     *
     * @return boolean
     */
    public function is_form_available() {
        return true; // todo make this dependent on grading_definitions existence and its status
    }

    /**
     * Prepare a grading widget for the given rater and item
     *
     * If you make multiple widgets, pass bulk = true. Note that then it is
     * the caller's responsibility to call {@link finalize_page()} method explicitly.
     *
     * @param int $raterid the user who will use the widget for grading
     * @param int $itemid the graded item
     * @param bool $bulk are more widgets to be made by this instance or is this the last one?
     * @return gradingform_widget renderable widget to insert into the page
     */
    abstract public function make_grading_widget($raterid, $itemid, $bulk = false);

    /**
     * Does everything needed before the page is sent to the browser
     */
    public function finalize_page() {
        $this->pagefinalized = true;
    }

    /**
     * Extends the module settings navigation
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING, the user has the permission moodle/grade:managegradingforms
     * and there is an area with the active grading method set to the given plugin.
     *
     * @param settings_navigation $settingsnav {@link settings_navigation}
     * @param navigation_node $node {@link navigation_node}
     */
    public function extend_settings_navigation(settings_navigation $settingsnav, navigation_node $node=null) {
        // do not extend by default
    }

    /**
     * Returns the name of the grading method, eg 'rubric'
     */
    abstract protected function get_method_name();

    /**
     * Sets the target page and returns a renderer for this plugin
     *
     * @param moodle_page $page the target page
     * @return core_renderer
     */
    public function prepare_renderer(moodle_page $page) {
        global $CFG;

        $this->page = $page;
        require_once($CFG->dirroot.'/grade/grading/form/'.$this->get_method_name().'/renderer.php');
        return $page->get_renderer('gradingform_'.$this->get_method_name());
    }

    /**
     * Loads the form definition is it exists
     *
     * The default implementation tries to load just the record ftom the {grading_definitions}
     * table. The plugins are likely to override this with a more complex query that loads
     * all required data at once.
     */
    protected function load_definition() {
        global $DB;

        $this->definition = $DB->get_record('grading_definitions', array(
            'areaid' => $this->areaid,
            'method' => $this->get_method_name()), '*', IGNORE_MISSING);
    }
}


/**
 * Base class for all gradingform plugins renderers
 */
abstract class gradingform_renderer extends plugin_renderer_base {
}


/**
 * Base class for all gradingform renderable widgets
 */
abstract class gradingform_widget implements renderable {
}
