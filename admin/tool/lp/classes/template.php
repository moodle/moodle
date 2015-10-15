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
 * @package    tool_lp
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lp;

use context;
use lang_string;
use stdClass;

/**
 * Class for loading/storing learning plan templates from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template extends persistent {

    const TABLE = 'tool_lp_template';

    /** @var template Object before update. */
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
                'type' => PARAM_TEXT,
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'idnumber' => array(
                'default' => '',
                'type' => PARAM_TEXT,
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
        if ($this->get_id()) {
            $this->beforeupdate = new self($this->get_id());
        }
    }

    /**
     * Get the context.
     *
     * @return context The context
     */
    public function get_context() {
        return context::instance_by_id($this->get_contextid());
    }

    /**
     * Validate the context ID.
     *
     * @param  int $value The context ID.
     * @return bool|lang_string
     */
    public function validate_contextid($value) {
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
     * @param  int $value The due date.
     * @return bool|lang_string
     */
    protected function validate_duedate($value) {

        // During update.
        if ($this->get_id()) {
            $before = $this->beforeupdate->get_duedate();
            if (!empty($before) && $before < time() && $value != $before) {
                // We cannot set a due date after it was reached.
                return new lang_string('errorcannotchangeapastduedate', 'tool_lp');
            }
        }

        // During create and update.
        if (!empty($value) && $value < time()) {
            // We cannot set the date in the past, but we can leave it empty.
            return new lang_string('errorcannotsetduedateinthepast', 'tool_lp');
        }

        return true;
    }

}
