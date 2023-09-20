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

namespace core_external;

use coding_exception;
use context;
use context_helper;
use context_system;
use core_component;
use core_php_time_limit;
use invalid_parameter_exception;
use invalid_response_exception;
use moodle_exception;

/**
 * Base class for external api methods.
 *
 * @package    core_webservice
 * @copyright  2009 Petr Skodak
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class external_api {

    /** @var \stdClass context where the function calls will be restricted */
    private static $contextrestriction;

    /**
     * Returns detailed function information
     *
     * @param string|\stdClass $function name of external function or record from external_function
     * @param int $strictness IGNORE_MISSING means compatible mode, false returned if record not found, debug message if more found;
     *                        MUST_EXIST means throw exception if no record or multiple records found
     * @return \stdClass|bool description or false if not found or exception thrown
     * @throws coding_exception for any property and/or method that is missing or invalid
     * @since Moodle 2.0
     */
    public static function external_function_info($function, $strictness = MUST_EXIST) {
        global $DB, $CFG;

        if (!is_object($function)) {
            if (!$function = $DB->get_record('external_functions', ['name' => $function], '*', $strictness)) {
                return false;
            }
        }

        // First try class autoloading.
        if (!class_exists($function->classname)) {
            // Fallback to explicit include of externallib.php.
            if (empty($function->classpath)) {
                $function->classpath = core_component::get_component_directory($function->component) . '/externallib.php';
            } else {
                $function->classpath = "{$CFG->dirroot}/{$function->classpath}";
            }
            if (!file_exists($function->classpath)) {
                throw new coding_exception(
                    "Cannot find file {$function->classpath} with external function implementation " .
                        "for {$function->classname}::{$function->methodname}"
                );
            }
            require_once($function->classpath);
            if (!class_exists($function->classname)) {
                throw new coding_exception("Cannot find external class {$function->classname}");
            }
        }

        $function->ajax_method = "{$function->methodname}_is_allowed_from_ajax";
        $function->parameters_method = "{$function->methodname}_parameters";
        $function->returns_method    = "{$function->methodname}_returns";
        $function->deprecated_method = "{$function->methodname}_is_deprecated";

        // Make sure the implementaion class is ok.
        if (!method_exists($function->classname, $function->methodname)) {
            throw new coding_exception(
                "Missing implementation method {$function->classname}::{$function->methodname}"
            );
        }
        if (!method_exists($function->classname, $function->parameters_method)) {
            throw new coding_exception(
                "Missing parameters description method {$function->classname}::{$function->parameters_method}"
            );
        }
        if (!method_exists($function->classname, $function->returns_method)) {
            throw new coding_exception(
                "Missing returned values description method {$function->classname}::{$function->returns_method}"
            );
        }
        if (method_exists($function->classname, $function->deprecated_method)) {
            if (call_user_func([$function->classname, $function->deprecated_method]) === true) {
                $function->deprecated = true;
            }
        }
        $function->allowed_from_ajax = false;

        // Fetch the parameters description.
        $function->parameters_desc = call_user_func([$function->classname, $function->parameters_method]);
        if (!($function->parameters_desc instanceof external_function_parameters)) {
            throw new coding_exception(
                "{$function->classname}::{$function->parameters_method} did not return a valid external_function_parameters object"
            );
        }

        // Fetch the return values description.
        $function->returns_desc = call_user_func([$function->classname, $function->returns_method]);
        // Null means void result or result is ignored.
        if (!is_null($function->returns_desc) && !($function->returns_desc instanceof external_description)) {
            throw new coding_exception(
                "{$function->classname}::{$function->returns_method} did not return a valid external_description object"
            );
        }

        // Now get the function description.

        // TODO MDL-31115 use localised lang pack descriptions, it would be nice to have
        // easy to understand descriptions in admin UI,
        // on the other hand this is still a bit in a flux and we need to find some new naming
        // conventions for these descriptions in lang packs.
        $function->description = null;
        $servicesfile = core_component::get_component_directory($function->component) . '/db/services.php';
        if (file_exists($servicesfile)) {
            $functions = null;
            include($servicesfile);
            if (isset($functions[$function->name]['description'])) {
                $function->description = $functions[$function->name]['description'];
            }
            if (isset($functions[$function->name]['testclientpath'])) {
                $function->testclientpath = $functions[$function->name]['testclientpath'];
            }
            if (isset($functions[$function->name]['type'])) {
                $function->type = $functions[$function->name]['type'];
            }
            if (isset($functions[$function->name]['ajax'])) {
                $function->allowed_from_ajax = $functions[$function->name]['ajax'];
            } else if (method_exists($function->classname, $function->ajax_method)) {
                if (call_user_func([$function->classname, $function->ajax_method]) === true) {
                    debugging('External function ' . $function->ajax_method . '() function is deprecated.' .
                              'Set ajax=>true in db/service.php instead.', DEBUG_DEVELOPER);
                    $function->allowed_from_ajax = true;
                }
            }
            if (isset($functions[$function->name]['loginrequired'])) {
                $function->loginrequired = $functions[$function->name]['loginrequired'];
            } else {
                $function->loginrequired = true;
            }
            if (isset($functions[$function->name]['readonlysession'])) {
                $function->readonlysession = $functions[$function->name]['readonlysession'];
            } else {
                $function->readonlysession = false;
            }
        }

        return $function;
    }

    /**
     * Call an external function validating all params/returns correctly.
     *
     * Note that an external function may modify the state of the current page, so this wrapper
     * saves and restores tha PAGE and COURSE global variables before/after calling the external function.
     *
     * @param string $function A webservice function name.
     * @param array $args Params array (named params)
     * @param boolean $ajaxonly If true, an extra check will be peformed to see if ajax is required.
     * @return array containing keys for error (bool), exception and data.
     */
    public static function call_external_function($function, $args, $ajaxonly = false) {
        global $PAGE, $COURSE, $CFG, $SITE;

        require_once("{$CFG->libdir}/pagelib.php");

        $externalfunctioninfo = static::external_function_info($function);

        // Eventually this should shift into the various handlers and not be handled via config.
        $readonlysession = $externalfunctioninfo->readonlysession ?? false;
        if (!$readonlysession || empty($CFG->enable_read_only_sessions)) {
            \core\session\manager::restart_with_write_lock($readonlysession);
        }

        $currentpage = $PAGE;
        $currentcourse = $COURSE;
        $response = [];

        try {
            // Taken straight from from setup.php.
            if (!empty($CFG->moodlepageclass)) {
                if (!empty($CFG->moodlepageclassfile)) {
                    require_once($CFG->moodlepageclassfile);
                }
                $classname = $CFG->moodlepageclass;
            } else {
                $classname = 'moodle_page';
            }
            $PAGE = new $classname();
            $COURSE = clone($SITE);

            if ($ajaxonly && !$externalfunctioninfo->allowed_from_ajax) {
                throw new moodle_exception('servicenotavailable', 'webservice');
            }

            // Do not allow access to write or delete webservices as a public user.
            if ($externalfunctioninfo->loginrequired && !WS_SERVER) {
                if (defined('NO_MOODLE_COOKIES') && NO_MOODLE_COOKIES && !PHPUNIT_TEST) {
                    throw new moodle_exception('servicerequireslogin', 'webservice');
                }
                if (!isloggedin()) {
                    throw new moodle_exception('servicerequireslogin', 'webservice');
                } else {
                    require_sesskey();
                }
            }
            // Validate params, this also sorts the params properly, we need the correct order in the next part.
            $callable = [$externalfunctioninfo->classname, 'validate_parameters'];
            $params = call_user_func(
                $callable,
                $externalfunctioninfo->parameters_desc,
                $args
            );
            $params = array_values($params);

            // Allow any Moodle plugin a chance to override this call. This is a convenient spot to
            // make arbitrary behaviour customisations. The overriding plugin could call the 'real'
            // function first and then modify the results, or it could do a completely separate
            // thing.
            $callbacks = get_plugins_with_function('override_webservice_execution');
            $result = false;
            foreach (array_values($callbacks) as $plugins) {
                foreach (array_values($plugins) as $callback) {
                    $result = $callback($externalfunctioninfo, $params);
                    if ($result !== false) {
                        break 2;
                    }
                }
            }

            // If the function was not overridden, call the real one.
            if ($result === false) {
                $callable = [$externalfunctioninfo->classname, $externalfunctioninfo->methodname];
                $result = call_user_func_array($callable, $params);
            }

            // Validate the return parameters.
            if ($externalfunctioninfo->returns_desc !== null) {
                $callable = [$externalfunctioninfo->classname, 'clean_returnvalue'];
                $result = call_user_func($callable, $externalfunctioninfo->returns_desc, $result);
            }

            $response['error'] = false;
            $response['data'] = $result;
        } catch (\Throwable $e) {
            $exception = get_exception_info($e);
            unset($exception->a);
            $exception->backtrace = format_backtrace($exception->backtrace, true);
            if (!debugging('', DEBUG_DEVELOPER)) {
                unset($exception->debuginfo);
                unset($exception->backtrace);
            }
            $response['error'] = true;
            $response['exception'] = $exception;
            // Do not process the remaining requests.
        }

        $PAGE = $currentpage;
        $COURSE = $currentcourse;

        return $response;
    }

    /**
     * Set context restriction for all following subsequent function calls.
     *
     * @param \stdClass $context the context restriction
     * @since Moodle 2.0
     */
    public static function set_context_restriction($context) {
        self::$contextrestriction = $context;
    }

    /**
     * This method has to be called before every operation
     * that takes a longer time to finish!
     *
     * @param int $seconds max expected time the next operation needs
     * @since Moodle 2.0
     */
    public static function set_timeout($seconds = 360) {
        $seconds = ($seconds < 300) ? 300 : $seconds;
        core_php_time_limit::raise($seconds);
    }

    /**
     * Validates submitted function parameters, if anything is incorrect
     * invalid_parameter_exception is thrown.
     * This is a simple recursive method which is intended to be called from
     * each implementation method of external API.
     *
     * @param external_description $description description of parameters
     * @param mixed $params the actual parameters
     * @return mixed params with added defaults for optional items, invalid_parameters_exception thrown if any problem found
     * @since Moodle 2.0
     */
    public static function validate_parameters(external_description $description, $params) {
        if ($description instanceof external_value) {
            if (is_array($params) || is_object($params)) {
                throw new invalid_parameter_exception('Scalar type expected, array or object received.');
            }

            if ($description->type == PARAM_BOOL) {
                // Special case for PARAM_BOOL - we want true/false instead of the usual 1/0 - we can not be too strict here.
                if (is_bool($params) || $params === 0 || $params === 1 || $params === '0' || $params === '1') {
                    return (bool) $params;
                }
            }
            $debuginfo = "Invalid external api parameter: the value is \"{$params}\", ";
            $debuginfo .= "the server was expecting \"{$description->type}\" type";
            return validate_param($params, $description->type, $description->allownull, $debuginfo);
        } else if ($description instanceof external_single_structure) {
            if (!is_array($params)) {
                throw new invalid_parameter_exception(
                    // phpcs:ignore moodle.PHP.ForbiddenFunctions.Found
                    "Only arrays accepted. The bad value is: '" . print_r($params, true) . "'"
                );
            }
            $result = [];
            foreach ($description->keys as $key => $subdesc) {
                if (!array_key_exists($key, $params)) {
                    if ($subdesc->required == VALUE_REQUIRED) {
                        throw new invalid_parameter_exception("Missing required key in single structure: {$key}");
                    }
                    if ($subdesc->required == VALUE_DEFAULT) {
                        try {
                            $result[$key] = static::validate_parameters($subdesc, $subdesc->default);
                        } catch (invalid_parameter_exception $e) {
                            // We are only interested by exceptions returned by validate_param() and validate_parameters().
                            // This is used to build the path to the faulty attribute.
                            throw new invalid_parameter_exception("{$key} => " . $e->getMessage() . ': ' . $e->debuginfo);
                        }
                    }
                } else {
                    try {
                        $result[$key] = static::validate_parameters($subdesc, $params[$key]);
                    } catch (invalid_parameter_exception $e) {
                        // We are only interested by exceptions returned by validate_param() and validate_parameters().
                        // This is used to build the path to the faulty attribute.
                        throw new invalid_parameter_exception($key . " => " . $e->getMessage() . ': ' . $e->debuginfo);
                    }
                }
                unset($params[$key]);
            }
            if (!empty($params)) {
                throw new invalid_parameter_exception(
                    'Unexpected keys (' . implode(', ', array_keys($params)) . ') detected in parameter array.'
                );
            }
            return $result;
        } else if ($description instanceof external_multiple_structure) {
            if (!is_array($params)) {
                throw new invalid_parameter_exception(
                    'Only arrays accepted. The bad value is: \'' .
                    // phpcs:ignore moodle.PHP.ForbiddenFunctions.Found
                    print_r($params, true) .
                    "'"
                );
            }
            $result = [];
            foreach ($params as $param) {
                $result[] = static::validate_parameters($description->content, $param);
            }
            return $result;
        } else {
            throw new invalid_parameter_exception('Invalid external api description');
        }
    }

    /**
     * Clean response
     * If a response attribute is unknown from the description, we just ignore the attribute.
     * If a response attribute is incorrect, invalid_response_exception is thrown.
     * Note: this function is similar to validate parameters, however it is distinct because
     * parameters validation must be distinct from cleaning return values.
     *
     * @param external_description $description description of the return values
     * @param mixed $response the actual response
     * @return mixed response with added defaults for optional items, invalid_response_exception thrown if any problem found
     * @author 2010 Jerome Mouneyrac
     * @since Moodle 2.0
     */
    public static function clean_returnvalue(external_description $description, $response) {
        if ($description instanceof external_value) {
            if (is_array($response) || is_object($response)) {
                throw new invalid_response_exception('Scalar type expected, array or object received.');
            }

            if ($description->type == PARAM_BOOL) {
                // Special case for PARAM_BOOL - we want true/false instead of the usual 1/0 - we can not be too strict here.
                if (is_bool($response) || $response === 0 || $response === 1 || $response === '0' || $response === '1') {
                    return (bool) $response;
                }
            }
            $responsetype = gettype($response);
            $debuginfo = "Invalid external api response: the value is \"{$response}\" of PHP type \"{$responsetype}\", ";
            $debuginfo .= "the server was expecting \"{$description->type}\" type";
            try {
                return validate_param($response, $description->type, $description->allownull, $debuginfo);
            } catch (invalid_parameter_exception $e) {
                // Proper exception name, to be recursively catched to build the path to the faulty attribute.
                throw new invalid_response_exception($e->debuginfo);
            }
        } else if ($description instanceof external_single_structure) {
            if (!is_array($response) && !is_object($response)) {
                throw new invalid_response_exception(
                    // phpcs:ignore moodle.PHP.ForbiddenFunctions.Found
                    "Only arrays/objects accepted. The bad value is: '" . print_r($response, true) . "'"
                );
            }

            // Cast objects into arrays.
            if (is_object($response)) {
                $response = (array) $response;
            }

            $result = [];
            foreach ($description->keys as $key => $subdesc) {
                if (!array_key_exists($key, $response)) {
                    if ($subdesc->required == VALUE_REQUIRED) {
                        throw new invalid_response_exception(
                            "Error in response - Missing following required key in a single structure: {$key}"
                        );
                    }
                    if ($subdesc instanceof external_value) {
                        if ($subdesc->required == VALUE_DEFAULT) {
                            try {
                                $result[$key] = static::clean_returnvalue($subdesc, $subdesc->default);
                            } catch (invalid_response_exception $e) {
                                // Build the path to the faulty attribute.
                                throw new invalid_response_exception("{$key} => " . $e->getMessage() . ': ' . $e->debuginfo);
                            }
                        }
                    }
                } else {
                    try {
                        $result[$key] = static::clean_returnvalue($subdesc, $response[$key]);
                    } catch (invalid_response_exception $e) {
                        // Build the path to the faulty attribute.
                        throw new invalid_response_exception("{$key} => " . $e->getMessage() . ': ' . $e->debuginfo);
                    }
                }
                unset($response[$key]);
            }

            return $result;
        } else if ($description instanceof external_multiple_structure) {
            if (!is_array($response)) {
                throw new invalid_response_exception(
                    // phpcs:ignore moodle.PHP.ForbiddenFunctions.Found
                    "Only arrays accepted. The bad value is: '" . print_r($response, true) . "'"
                );
            }
            $result = [];
            foreach ($response as $param) {
                $result[] = static::clean_returnvalue($description->content, $param);
            }
            return $result;
        } else {
            throw new invalid_response_exception('Invalid external api response description');
        }
    }

    /**
     * Makes sure user may execute functions in this context.
     *
     * @param context $context
     * @since Moodle 2.0
     */
    public static function validate_context($context) {
        global $PAGE;

        if (empty($context)) {
            throw new invalid_parameter_exception('Context does not exist');
        }
        if (empty(self::$contextrestriction)) {
            self::$contextrestriction = context_system::instance();
        }
        $rcontext = self::$contextrestriction;

        if ($rcontext->contextlevel == $context->contextlevel) {
            if ($rcontext->id != $context->id) {
                throw new restricted_context_exception();
            }
        } else if ($rcontext->contextlevel > $context->contextlevel) {
            throw new restricted_context_exception();
        } else {
            $parents = $context->get_parent_context_ids();
            if (!in_array($rcontext->id, $parents)) {
                throw new restricted_context_exception();
            }
        }

        $PAGE->reset_theme_and_output();
        [, $course, $cm] = get_context_info_array($context->id);
        require_login($course, false, $cm, false, true);
        $PAGE->set_context($context);
    }

    /**
     * Get context from passed parameters.
     * The passed array must either contain a contextid or a combination of context level and instance id to fetch the context.
     * For example, the context level can be "course" and instanceid can be courseid.
     *
     * See context_helper::get_all_levels() for a list of valid numeric context levels,
     * legacy short names such as 'system', 'user', 'course' are not supported in new
     * plugin capabilities.
     *
     * @param array $param
     * @since Moodle 2.6
     * @throws invalid_parameter_exception
     * @return context
     */
    protected static function get_context_from_params($param) {
        if (!empty($param['contextid'])) {
            return context::instance_by_id($param['contextid'], IGNORE_MISSING);
        } else if (!empty($param['contextlevel']) && isset($param['instanceid'])) {
            // Numbers and short names are supported since Moodle 4.2.
            $classname = \core\context_helper::parse_external_level($param['contextlevel']);
            if (!$classname) {
                throw new invalid_parameter_exception('Invalid context level = '.$param['contextlevel']);
            }
            return $classname::instance($param['instanceid'], IGNORE_MISSING);
        } else {
            // No valid context info was found.
            throw new invalid_parameter_exception(
                'Missing parameters, please provide either context level with instance id or contextid'
            );
        }
    }

    /**
     * Returns a prepared structure to use a context parameters.
     * @return external_single_structure
     */
    protected static function get_context_parameters() {
        $id = new external_value(
            PARAM_INT,
            'Context ID. Either use this value, or level and instanceid.',
            VALUE_DEFAULT,
            0
        );
        $level = new external_value(
            PARAM_ALPHANUM, // Since Moodle 4.2 numeric context level values are supported too.
            'Context level. To be used with instanceid.',
            VALUE_DEFAULT,
            ''
        );
        $instanceid = new external_value(
            PARAM_INT,
            'Context instance ID. To be used with level',
            VALUE_DEFAULT,
            0
        );
        return new external_single_structure([
            'contextid' => $id,
            'contextlevel' => $level,
            'instanceid' => $instanceid,
        ]);
    }
}
