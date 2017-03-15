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
 * Class for loading/storing learning plan templates from the DB.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use context;
use lang_string;
use stdClass;

require_once($CFG->dirroot . '/local/iomad/lib/iomad.php');

/**
 * Class for loading/storing learning plan templates from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template extends persistent {

    const TABLE = 'competency_template';

    /** @var template object before update. */
    protected $beforeupdate = null;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'shortname' => array(
                'type' => PARAM_TEXT,
            ),
            'description' => array(
                'default' => '',
                'type' => PARAM_CLEANHTML,
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'duedate' => array(
                'default' => 0,
                'type' => PARAM_INT,
            ),
            'visible' => array(
                'default' => 1,
                'type' => PARAM_BOOL,
            ),
            'contextid' => array(
                'type' => PARAM_INT
            ),
        );
    }

    /**
     * Hook to execute after an update.
     *
     * @param bool $result Whether or not the update was successful.
     * @return void
     */
    protected function after_update($result) {
        $this->beforeupdate = null;
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        $this->beforeupdate = null;

        // During update.
        if ($this->get('id')) {
            $this->beforeupdate = new self($this->get('id'));
        }
    }

    /**
     * Whether or not the current user can read the template.
     *
     * @return bool
     */
    public function can_manage() {
        return self::can_manage_context($this->get_context());
    }

    /**
     * Whether or not the current user can manage the template.
     *
     * @param  context $context
     * @return bool
     */
    public static function can_manage_context($context) {
        return \iomad::has_capability('moodle/competency:templatemanage', $context);
    }

    /**
     * Whether or not the current user can read the template.
     *
     * @return bool
     */
    public function can_read() {
        return self::can_read_context($this->get_context());
    }

    /**
     * Whether or not the current user can read the template.
     *
     * @param  context $context
     * @return bool
     */
    public static function can_read_context($context) {
        return \iomad::has_capability('moodle/competency:templateview', $context) || self::can_manage_context($context);
    }

    /**
     * Get the context.
     *
     * @return context The context
     */
    public function get_context() {
        return context::instance_by_id($this->get('contextid'));
    }

    /**
     * Validate the context ID.
     *
     * @param  int $value The context ID.
     * @return bool|lang_string
     */
    protected function validate_contextid($value) {
        $context = context::instance_by_id($value, IGNORE_MISSING);
        if (!$context) {
            return new lang_string('invalidcontext', 'error');
        } else if ($context->contextlevel != CONTEXT_SYSTEM && $context->contextlevel != CONTEXT_COURSECAT) {
            return new lang_string('invalidcontext', 'error');
        }
        return true;
    }

    /**
     * Validate the due date.
     *
     * The due date can always be changed, but when it is it must be:
     *  - unset
     *  - set in the future.
     *
     * @param  int $value The due date.
     * @return bool|lang_string
     */
    protected function validate_duedate($value) {

        // During update.
        if ($this->get('id')) {
            $before = $this->beforeupdate->get('duedate');

            // The value has not changed, then it's always OK.
            if ($before == $value) {
                return true;
            }
        }

        // During create and update, the date must be set in the future, or not set.
        if (!empty($value) && $value <= time() - 600) {
            // We cannot set the date in the past. But we allow for 10 minutes of margin so that
            // a user can set the due date to "now" without risking to hit a validation error.
            return new lang_string('errorcannotsetduedateinthepast', 'core_competency');
        }

        return true;
    }

    /**
     * Returns true when the template has user learning plans.
     *
     * @return boolean
     */
    public function has_plans() {
        return plan::has_records_for_template($this->get('id'));
    }

}
