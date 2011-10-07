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
 * Common classes used by gradingform plugintypes are defined here
 *
 * @package    core
 * @subpackage grading
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Grading method controller represents a plugin used in a particular area
 */
abstract class gradingform_controller {

    const DEFINITION_STATUS_WORKINPROGRESS  = 0;
    const DEFINITION_STATUS_PRIVATE         = 1;
    const DEFINITION_STATUS_PUBLIC          = 2;

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

    /** @var array list of widgets made by this controller for $this->page */
    protected $widgets = array();

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
     * Is the grading form defined and released for usage by the given user?
     *
     * @param int $foruserid the id of the user who attempts to work with the form
     * @return boolean
     */
    public function is_form_available($foruserid = null) {
        global $USER;

        if (is_null($foruserid)) {
            $foruserid = $USER->id;
        }

        if (empty($this->definition)) {
            return false;
        }

        if ($this->definition->status == self::DEFINITION_STATUS_PUBLIC) {
            return true;
        }

        if ($this->definition->status == self::DEFINITION_STATUS_PRIVATE) {
            if ($this->definition->usercreated == $foruserid) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prepare a grading widget for the given rater and item
     *
     * The options array MUST contain (string)displayas set to either 'scale' or 'grade'.
     * If scale is used, the (int)scaleid must be provided. If grade is used, (int)maxgrade
     * must be provided and (int)decimals can be provided (defaults to 0).
     * The options array CAN contain (bool)bulk to signalize whether there are more widgets to be
     * made by this controller instance or whether this is the last one.
     * If you make multiple widgets, pass bulk option se to true. Note that then it is
     * the caller's responsibility to call {@link finalize_page()} method explicitly then.
     *
     * @param int $raterid the user who will use the widget for grading
     * @param int $itemid the graded item
     * @param array $options the widget options
     * @return gradingform_widget renderable widget to insert into the page
     */
    abstract public function make_grading_widget($raterid, $itemid, array $options);

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
     * Saves the defintion data into database
     *
     * The default implementation stored the record into the {grading_definition} table. The
     * plugins are likely to extend this and save their data into own tables, too.
     *
     * @param stdClass $definition
     */
    public function update_definition(stdClass $definition, $usermodified = null) {
        global $DB, $USER;

        if (is_null($usermodified)) {
            $usermodified = $USER->id;
        }

        if ($this->definition) {
            // the following fields can't be altered by the caller
            $definition->id             = $this->definition->id;
            $definition->timemodified   = time();
            $definition->usermodified   = $usermodified;
            unset($definition->areaid);
            unset($definition->method);
            unset($definition->timecreated);

            $DB->update_record('grading_definitions', $definition);

        } else if ($this->definition === false) {
            // the record did not existed when the controller was instantinated
            // let us assume it still does not exist (this may throw exception
            // in case of two users working on the same form definition at the same time)
            unset($definition->id);
            $definition->areaid         = $this->areaid;
            $definition->method         = $this->get_method_name();
            $definition->timecreated    = time();
            $definition->usercreated    = $usermodified;
            $definition->timemodified   = $definition->timecreated;
            $definition->usermodified   = $definition->usercreated;
            $definition->status         = self::DEFINITION_STATUS_WORKINPROGRESS;

            $DB->insert_record('grading_definitions', $definition);

        } else {
            // this should not happen - the record cache status is unknown, let us
            // reload it and start again
            $this->load_definition();
            $this->update_definition($definition, $usermodified);
        }
    }

    /**
     * Makes sure there is a form instance for the given rater grading the given item
     *
     * Plugins will probably override/extend this and load additional data of how their
     * forms are filled in one complex query.
     *
     * @todo this might actually become abstract method
     * @param int $raterid
     * @param int $itemid
     * @return stdClass newly created or existing record from {grading_instances}
     */
    public function prepare_instance($raterid, $itemid) {
        global $DB;

        if (empty($this->definition)) {
            throw new coding_exception('Attempting to prepare an instance of non-existing grading form');
        }

        $current = $DB->get_record('grading_instances', array(
            'formid'  => $this->definition->id,
            'raterid' => $raterid,
            'itemid'  => $itemid), '*', IGNORE_MISSING);

        if (empty($current)) {
            $instance = new stdClass();
            $instance->formid = $this->definition->id;
            $instance->raterid = $raterid;
            $instance->itemid = $itemid;
            $instance->timemodified = time();
            $instance->id = $DB->insert_record('grading_instances', $instance);
            return $instance;

        } else {
            return $current;
        }
    }

    /**
     * Loads the form definition if it exists
     *
     * The default implementation just tries to load the record from the {grading_definitions}
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

    /** @var string unique identifier that can be used as the element id during the rendering, for example */
    public $id;

    /** @var gradingform_controller instance of the controller that created this widget */
    public $controller;

    /** @var array the widget options */
    public $options;

    /** @var stdClass the current instance data */
    public $instance;

    /**
     * @param gradingform_controller the method controller that created this widget
     */
    public function __construct(gradingform_controller $controller, array $options, stdClass $instance) {
        $this->id         = uniqid(get_class($this));
        $this->controller = $controller;
        $this->options    = $options;
        $this->instance   = $instance;
    }
}


/**
 * Base class for all gradingform renderable grading widgets
 *
 * Grading widget is the UI element that raters use when they interact with
 * the grading form.
 */
abstract class gradingform_grading_widget extends gradingform_widget {
}


/**
 * Base class for all gradingform renderable editor widgets
 *
 * Plugins that implement editor UI via its own renderable widgets should
 * probably extend this. Editor widget is the UI element that course designers
 * use when they design (define) the grading form.
 */
abstract class gradingform_editor_widget implements renderable {
}
