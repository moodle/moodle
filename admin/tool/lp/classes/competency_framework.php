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
 * Class for loading/storing competency frameworks from the DB.
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
 * Class for loading/storing competency frameworks from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_framework extends persistent {

    const TABLE = 'tool_lp_competency_framework';

    /**
     * Get the context.
     *
     * @return context The context
     */
    public function get_context() {
        return context::instance_by_id($this->get_contextid());
    }

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'shortname' => array(
                'type' => PARAM_TEXT
            ),
            'idnumber' => array(
                'type' => PARAM_TEXT
            ),
            'description' => array(
                'type' => PARAM_TEXT,
                'default' => ''
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'visible' => array(
                'type' => PARAM_BOOL,
                'default' => 1
            ),
            // TODO MDL-51442 make this mandatory.
            'scaleid' => array(
                'type' => PARAM_INT,
                'default' => 0
            ),
            // TODO MDL-51442 make this mandatory.
            'scaleconfiguration' => array(
                'type' => PARAM_RAW,
                'default' => ''
            ),
            'contextid' => array(
                'type' => PARAM_INT
            ),
        );
    }

    /**
     * Validate the context ID.
     *
     * @param  int $value The context ID.
     * @return bool|lang_string
     */
    public function validate_contextid($value) {
        global $DB;

        $context = context::instance_by_id($value, IGNORE_MISSING);
        if (!$context) {
            return new lang_string('invalidcontext', 'error');
        } else if ($context->contextlevel != CONTEXT_SYSTEM && $context->contextlevel != CONTEXT_COURSECAT) {
            return new lang_string('invalidcontext', 'error');
        }

        // During update.
        if ($this->get_id()) {

            // The context must never change.
            $oldcontextid = $DB->get_field(self::TABLE, 'contextid', array('id' => $this->get_id()), MUST_EXIST);
            if ($this->get_contextid() != $oldcontextid) {
                return new lang_string('invalidcontext', 'error');
            }
        }

        return true;
    }

}
