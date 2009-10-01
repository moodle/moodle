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
    public static function validate_prameters($description, $params) {
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
}

/**
 * Common ancestor of all parameter description classes
 */
abstract class external_description {
    /** @property string $description description of element */
    public $desc;
    /** @property bool $required element value required, null not alowed */
    public $required;

    /**
     * Contructor
     * @param string $desc
     * @param bool $required
     */
    public function __contruct($desc, $required) {
        $this->desc = $desc;
        $this->required = $required;
    }
}

/**
 * Scalar parameter description class
 */
class external_param extends external_description {
    /** @property mixed $type parameter type PARAM_XX */
    public $type;
    /** @property mixed $default default value */
    public $default;
    /** @property bool $allownull allow null values */
    public $allownull;

    /**
     * Constructor
     * @param mixed $type
     * @param string $desc
     * @param bool $required
     * @param mixed $default
     * @param bool $allownull
     */
    public function __contruct($type, $desc='', $required=true, $default=null, $allownull=true) {
        parent::_construct($desc, $required);
        $this->type      = $type;
        $this->default   = $default;
        $this->allownull = $allownull;
    }
}

/**
 * Associative array description class
 */
class external_single_structure extends external_description {
     /** @property array $keys description of array keys key=>external_description */
    public $keys;

    /**
     * Constructor
     * @param array $keys
     * @param string $desc
     * @param bool $required
     */
    public function __construct(array $keys, $desc='', $required=true) {
        parent::_construct($desc, $required);
        $this->keys = $keys;
    }
}

/**
 * Bulk array description class.
 */
class external_multiple_structure extends external_description {
     /** @property external_description $content */
    public $content;

    /**
     * Constructor
     * @param external_description $content
     * @param string $desc
     * @param bool $required
     */
    public function __construct(external_description $content, $desc='', $required=true) {
        parent::_construct($desc, $required);
        $this->content = $content;
    }
}
