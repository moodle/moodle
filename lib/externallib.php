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
 * Support for external API
 *
 * @package    moodlecore
 * @subpackage webservice
 * @copyright  2008 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Exception indicating user is not allowed to use external function in
 * the current context.
 */
class restricted_context_exception extends moodle_exception {
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct('restrictedcontextexception', 'error');
    }
}

/**
 * Base class for external api methods.
 */
class external_api {
    private static $contextrestriction;

    /**
     * Set context restriction for all folowing subsequent function calls.
     * @param stdClass $contex
     * @return void
     */
    public static function set_context_restriction($contex) {
        self::$contextrestriction = $context;
    }

    /**
     * Validates submitted function barameters, if anything is incorrect
     * invalid_parameter_exception is thrown.
     * @param ? $description description of parameters
     * @param ? $params the actual parameters
     * @return ? params with added defaults for optional items, invalid_parameters_exception thrown if any problem found
     */
    public static function validate_params($description, $params) {
        //TODO: we need to define the structure of param descriptions

        return $params;
    }

    /**
     * Makes sure user may execute functions in this context.
     * @param object $context
     * @return void
     */
    protected static function validate_context($context) {
        if (empty($context)) {
            throw new invalid_parameter_exception('Context does not exist');
        }
        if (empty(self::$contextrestriction)) {
            self::$contextrestriction = get_context_instance(CONTEXT_SYSTEM);
        }
        $rcontext = self::$contextrestriction;

        if ($rcontext->contextlevel == $context->contextlevel) {
            if ($rcontex->id != $context->id) {
                throw new restricted_context_exception();
            }
        } else if ($rcontext->contextlevel > $context->contextlevel) {
            throw new restricted_context_exception();
        } else {
            $parents = get_parent_contexts($context);
            if (!in_array($rcontext->id, $parents)) {
                throw new restricted_context_exception();
            }
        }

        if ($context->contextlevel >= CONTEXT_COURSE) {
            //TODO: temporary bloody hack, this needs to be replaced by
            //      proper enrolment and course visibility check
            //      similar to require_login() (which can not be used
            //      because it can be used only once and redirects)
            //      oh - did I tell we need to rewrite enrolments in 2.0
            //      to solve this bloody mess?
            //
            //      missing: hidden courses and categories, groupmembersonly,
            //      conditional activities, etc.
            require_capability('moodle/course:view', $context);
        }
    }

    /**
     * Returns detailed information about external function
     * @param string $functionname name of external function
     * @return aray
     */
    public static function get_function_info($functionname) {
        global $CFG, $DB;

        $function = $DB->get_record('external_functions', array('name'=>$functionname), '*', MUST_EXIST);

        $defpath = get_component_directory($function->component);
        if (!file_exists("$defpath/db/services.php")) {
            //TODO: maybe better throw invalid parameter exception
            return null;
        }

        $functions = array();
        include("$defpath/db/services.php");

        if (empty($functions[$functionname])) {
            return null;
        }

        $desc = $functions[$functionname];
        if (empty($desc['classpath'])) {
            $desc['classpath'] = "$defpath/externallib.php";
        } else {
            $desc['classpath'] = "$CFG->dirroot/".$desc['classpath'];
        }
        $desc['component'] = $function->component;

        return $desc;
    }
}

