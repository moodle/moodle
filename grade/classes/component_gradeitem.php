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
 * Compontent definition of a gradeitem.
 *
 * @package   core_grades
 * @copyright Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace core_grades;

use context;
use gradingform_controller;
use gradingform_instance;
use moodle_exception;
use stdClass;
use grade_item as core_gradeitem;
use grading_manager;

/**
 * Compontent definition of a gradeitem.
 *
 * @package   core_grades
 * @copyright Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class component_gradeitem {

    /** @var array The scale data for the current grade item */
    protected $scale;

    /** @var string The component */
    protected $component;

    /** @var context The context for this activity */
    protected $context;

    /** @var string The item name */
    protected $itemname;

    /** @var int The grade itemnumber */
    protected $itemnumber;

    /**
     * component_gradeitem constructor.
     *
     * @param string $component
     * @param context $context
     * @param string $itemname
     * @throws \coding_exception
     */
    final protected function __construct(string $component, context $context, string $itemname) {
        $this->component = $component;
        $this->context = $context;
        $this->itemname = $itemname;
        $this->itemnumber = component_gradeitems::get_itemnumber_from_itemname($component, $itemname);
    }

    /**
     * Fetch an instance of a specific component_gradeitem.
     *
     * @param string $component
     * @param context $context
     * @param string $itemname
     * @return self
     */
    public static function instance(string $component, context $context, string $itemname): self {
        $itemnumber = component_gradeitems::get_itemnumber_from_itemname($component, $itemname);

        $classname = "{$component}\\grades\\{$itemname}_gradeitem";
        if (!class_exists($classname)) {
            throw new \coding_exception("Unknown gradeitem {$itemname} for component {$classname}");
        }

        return $classname::load_from_context($context);
    }

    /**
     * Load an instance of the current component_gradeitem based on context.
     *
     * @param context $context
     * @return self
     */
    abstract public static function load_from_context(context $context): self;

    /**
     * The table name used for grading.
     *
     * @return string
     */
    abstract protected function get_table_name(): string;

    /**
     * Get the itemid for the current gradeitem.
     *
     * @return int
     */
    public function get_grade_itemid(): int {
        return component_gradeitems::get_itemnumber_from_itemname($this->component, $this->itemname);
    }

    /**
     * Whether grading is enabled for this item.
     *
     * @return bool
     */
    abstract public function is_grading_enabled(): bool;

    /**
     * Get the grade value for this instance.
     * The itemname is translated to the relevant grade field for the activity.
     *
     * @return int
     */
    abstract protected function get_gradeitem_value(): ?int;

    /**
     * Whether the grader can grade the gradee.
     *
     * @param stdClass $gradeduser The user being graded
     * @param stdClass $grader The user who is grading
     * @return bool
     */
    abstract public function user_can_grade(stdClass $gradeduser, stdClass $grader): bool;

    /**
     * Require that the user can grade, throwing an exception if not.
     *
     * @param stdClass $gradeduser The user being graded
     * @param stdClass $grader The user who is grading
     * @throws \required_capability_exception
     */
    abstract public function require_user_can_grade(stdClass $gradeduser, stdClass $grader): void;

    /**
     * Get the scale if a scale is being used.
     *
     * @return stdClass
     */
    protected function get_scale(): ?stdClass {
        global $DB;

        $gradetype = $this->get_gradeitem_value();
        if ($gradetype > 0) {
            return null;
        }

        // This is a scale.
        if (null === $this->scale) {
            $this->scale = $DB->get_record('scale', ['id' => -1 * $gradetype]);
        }

        return $this->scale;
    }

    /**
     * Check whether a scale is being used for this grade item.
     *
     * @return bool
     */
    public function is_using_scale(): bool {
        $gradetype = $this->get_gradeitem_value();

        return $gradetype < 0;
    }

    /**
     * Whether this grade item is configured to use direct grading.
     *
     * @return bool
     */
    public function is_using_direct_grading(): bool {
        if ($this->is_using_scale()) {
            return false;
        }

        if ($this->get_advanced_grading_controller()) {
            return false;
        }

        return true;
    }

    /**
     * Whether this grade item is configured to use advanced grading.
     *
     * @return bool
     */
    public function is_using_advanced_grading(): bool {
        if ($this->is_using_scale()) {
            return false;
        }

        if ($this->get_advanced_grading_controller()) {
            return true;
        }

        return false;
    }

    /**
     * Get the name of the advanced grading method.
     *
     * @return string
     */
    public function get_advanced_grading_method(): ?string {
        $gradingmanager = $this->get_grading_manager();

        if (empty($gradingmanager)) {
            return null;
        }

        return $gradingmanager->get_active_method();
    }

    /**
     * Get the name of the component responsible for grading this gradeitem.
     *
     * @return string
     */
    public function get_grading_component_name(): ?string {
        if (!$this->is_grading_enabled()) {
            return null;
        }

        if ($method = $this->get_advanced_grading_method()) {
            return "gradingform_{$method}";
        }

        return 'core_grades';
    }

    /**
     * Get the name of the component subtype responsible for grading this gradeitem.
     *
     * @return string
     */
    public function get_grading_component_subtype(): ?string {
        if (!$this->is_grading_enabled()) {
            return null;
        }

        if ($method = $this->get_advanced_grading_method()) {
            return null;
        }

        if ($this->is_using_scale()) {
            return 'scale';
        }

        return 'point';
    }

    /**
     * Whether decimals are allowed.
     *
     * @return bool
     */
    protected function allow_decimals(): bool {
        return $this->get_gradeitem_value() > 0;
    }

    /**
     * Get the grading manager for this advanced grading definition.
     *
     * @return grading_manager
     */
    protected function get_grading_manager(): ?grading_manager {
        require_once(__DIR__ . '/../grading/lib.php');
        return get_grading_manager($this->context, $this->component, $this->itemname);

    }

    /**
     * Get the advanced grading controller if advanced grading is enabled.
     *
     * @return gradingform_controller
     */
    protected function get_advanced_grading_controller(): ?gradingform_controller {
        $gradingmanager = $this->get_grading_manager();

        if (empty($gradingmanager)) {
            return null;
        }

        if ($gradingmethod = $gradingmanager->get_active_method()) {
            return $gradingmanager->get_controller($gradingmethod);
        }

        return null;
    }

    /**
     * Get the list of available grade items.
     *
     * @return array
     */
    public function get_grade_menu(): array {
        return make_grades_menu($this->get_gradeitem_value());
    }

    /**
     * Check whether the supplied grade is valid and throw an exception if not.
     *
     * @param float $grade The value being checked
     * @throws moodle_exception
     * @return bool
     */
    public function check_grade_validity(?float $grade): bool {
        $grade = grade_floatval(unformat_float($grade));
        if ($grade) {
            if ($this->is_using_scale()) {
                // Fetch all options for this scale.
                $scaleoptions = make_menu_from_list($this->get_scale()->scale);

                if ($grade != -1 && !array_key_exists((int) $grade, $scaleoptions)) {
                    // The selected option did not exist.
                    throw new moodle_exception('error:notinrange', 'core_grading', '', (object) [
                        'maxgrade' => count($scaleoptions),
                        'grade' => $grade,
                    ]);
                }
            } else if ($grade) {
                $maxgrade = $this->get_gradeitem_value();
                if ($grade > $maxgrade) {
                    // The grade is greater than the maximum possible value.
                    throw new moodle_exception('error:notinrange', 'core_grading', '', (object) [
                        'maxgrade' => $maxgrade,
                        'grade' => $grade,
                    ]);
                } else if ($grade < 0) {
                    // Negative grades are not supported.
                    throw new moodle_exception('error:notinrange', 'core_grading', '', (object) [
                        'maxgrade' => $maxgrade,
                        'grade' => $grade,
                    ]);
                }
            }
        }

        return true;
    }

    /**
     * Create an empty row in the grade for the specified user and grader.
     *
     * @param stdClass $gradeduser The user being graded
     * @param stdClass $grader The user who is grading
     * @return stdClass The newly created grade record
     */
    abstract public function create_empty_grade(stdClass $gradeduser, stdClass $grader): stdClass;

    /**
     * Get the grade record for the specified grade id.
     *
     * @param int $gradeid
     * @return stdClass
     * @throws \dml_exception
     */
    public function get_grade(int $gradeid): stdClass {
        global $DB;

        return $DB->get_record($this->get_table_name(), ['id' => $gradeid]);
    }

    /**
     * Get the grade for the specified user.
     *
     * @param stdClass $gradeduser The user being graded
     * @param stdClass $grader The user who is grading
     * @return stdClass The grade value
     */
    abstract public function get_grade_for_user(stdClass $gradeduser, stdClass $grader): ?stdClass;

    /**
     * Returns the grade that should be displayed to the user.
     *
     * The grade does not necessarily return a float value, this method takes grade settings into considering so
     * the correct value be shown, eg. a float vs a letter.
     *
     * @param stdClass $gradeduser
     * @param stdClass $grader
     * @return stdClass|null
     */
    public function get_formatted_grade_for_user(stdClass $gradeduser, stdClass $grader): ?stdClass {
        global $DB;

        if ($grade = $this->get_grade_for_user($gradeduser, $grader)) {
            $gradeitem = $this->get_grade_item();
            if (!$this->is_using_scale()) {
                $grade->usergrade = grade_format_gradevalue($grade->grade, $gradeitem);
                $grade->maxgrade = format_float($gradeitem->grademax, $gradeitem->get_decimals());
                // If displaying the raw grade, also display the total value.
                if ($gradeitem->get_displaytype() == GRADE_DISPLAY_TYPE_REAL) {
                    $grade->usergrade .= ' / ' . $grade->maxgrade;
                }
            } else {
                $grade->usergrade = '-';
                if ($scale = $DB->get_record('scale', ['id' => $gradeitem->scaleid])) {
                    $options = make_menu_from_list($scale->scale);

                    $gradeint = (int) $grade->grade;
                    if (isset($options[$gradeint])) {
                        $grade->usergrade = $options[$gradeint];
                    }
                }

                $grade->maxgrade = format_float($gradeitem->grademax, $gradeitem->get_decimals());
            }

            return $grade;
        }

        return null;
    }

    /**
     * Get the grade status for the specified user.
     * If the user has a grade as defined by the implementor return true else return false.
     *
     * @param stdClass $gradeduser The user being graded
     * @return bool The grade status
     */
    abstract public function user_has_grade(stdClass $gradeduser): bool;

    /**
     * Get grades for all users for the specified gradeitem.
     *
     * @return stdClass[] The grades
     */
    abstract public function get_all_grades(): array;

    /**
     * Get the grade item instance id.
     *
     * This is typically the cmid in the case of an activity, and relates to the iteminstance field in the grade_items
     * table.
     *
     * @return int
     */
    abstract public function get_grade_instance_id(): int;

    /**
     * Get the core grade item from the current component grade item.
     * This is mainly used to access the max grade for a gradeitem
     *
     * @return \grade_item The grade item
     */
    public function get_grade_item(): \grade_item {
        global $CFG;
        require_once("{$CFG->libdir}/gradelib.php");

        [$itemtype, $itemmodule] = \core_component::normalize_component($this->component);
        $gradeitem = \grade_item::fetch([
            'itemtype' => $itemtype,
            'itemmodule' => $itemmodule,
            'itemnumber' => $this->itemnumber,
            'iteminstance' => $this->get_grade_instance_id(),
        ]);

        return $gradeitem;
    }

    /**
     * Create or update the grade.
     *
     * @param stdClass $grade
     * @return bool Success
     */
    abstract protected function store_grade(stdClass $grade): bool;

    /**
     * Create or update the grade.
     *
     * @param stdClass $gradeduser The user being graded
     * @param stdClass $grader The user who is grading
     * @param stdClass $formdata The data submitted
     * @return bool Success
     */
    public function store_grade_from_formdata(stdClass $gradeduser, stdClass $grader, stdClass $formdata): bool {
        // Require gradelib for grade_floatval.
        require_once(__DIR__ . '/../../lib/gradelib.php');
        $grade = $this->get_grade_for_user($gradeduser, $grader);

        if ($this->is_using_advanced_grading()) {
            $instanceid = $formdata->instanceid;
            $gradinginstance = $this->get_advanced_grading_instance($grader, $grade, (int) $instanceid);
            $grade->grade = $gradinginstance->submit_and_get_grade($formdata->advancedgrading, $grade->id);

            if ($grade->grade == -1) {
                // In advanced grading, a value of -1 means no data.
                return false;
            }
        } else {
            // Handle the case when grade is set to No Grade.
            if (isset($formdata->grade)) {
                $grade->grade = grade_floatval(unformat_float($formdata->grade));
            }
        }

        return $this->store_grade($grade);
    }

    /**
     * Get the advanced grading instance for the specified grade entry.
     *
     * @param stdClass $grader The user who is grading
     * @param stdClass $grade The row from the grade table.
     * @param int $instanceid The instanceid of the advanced grading form
     * @return gradingform_instance
     */
    public function get_advanced_grading_instance(stdClass $grader, stdClass $grade, int $instanceid = null): ?gradingform_instance {
        $controller = $this->get_advanced_grading_controller($this->itemname);

        if (empty($controller)) {
            // Advanced grading not enabeld for this item.
            return null;
        }

        if (!$controller->is_form_available()) {
            // The form is not available for this item.
            return null;
        }

        // Fetch the instance for the specified graderid/itemid.
        $gradinginstance = $controller->fetch_instance(
            (int) $grader->id,
            (int) $grade->id,
            $instanceid
        );

        // Set the allowed grade range.
        $gradinginstance->get_controller()->set_grade_range(
            $this->get_grade_menu(),
            $this->allow_decimals()
        );

        return $gradinginstance;
    }

    /**
     * Sends a notification about the item being graded for the student.
     *
     * @param stdClass $gradeduser The user being graded
     * @param stdClass $grader The user who is grading
     */
    public function send_student_notification(stdClass $gradeduser, stdClass $grader): void {
        $contextname = $this->context->get_context_name();
        $eventdata = new \core\message\message();
        $eventdata->courseid          = $this->context->get_course_context()->instanceid;
        $eventdata->component         = 'moodle';
        $eventdata->name              = 'gradenotifications';
        $eventdata->userfrom          = $grader;
        $eventdata->userto            = $gradeduser;
        $eventdata->subject           = get_string('gradenotificationsubject', 'grades');
        $eventdata->fullmessage       = get_string('gradenotificationmessage', 'grades', $contextname);
        $eventdata->contexturl        = $this->context->get_url();
        $eventdata->contexturlname    = $contextname;
        $eventdata->fullmessageformat = FORMAT_HTML;
        $eventdata->fullmessagehtml   = '';
        $eventdata->smallmessage      = '';
        $eventdata->notification      = 1;
        message_send($eventdata);
    }
}
