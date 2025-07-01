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

namespace core;

use coding_exception;
use core_text;
use core\attribute\deprecated;
use core\ip_utils;
use invalid_parameter_exception;
use moodle_exception;

// phpcs:disable Generic.CodeAnalysis.EmptyStatement.DetectedIf

/**
 * Parameter validation helpers for Moodle.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum param: string {
    /**
     * PARAM_ALPHA - contains only English ascii letters [a-zA-Z].
     */
    #[param_clientside_regex('^[a-zA-Z]+$')]
    case ALPHA = 'alpha';

    /**
     * PARAM_ALPHAEXT the same contents as PARAM_ALPHA (English ascii letters [a-zA-Z]) plus the chars in quotes: "_-" allowed
     * NOTE: originally this allowed "/" too, please use PARAM_SAFEPATH if "/" needed
     */
    #[param_clientside_regex('^[a-zA-Z_\-]*$')]
    case ALPHAEXT = 'alphaext';

    /**
     * PARAM_ALPHANUM - expected numbers 0-9 and English ascii letters [a-zA-Z] only.
     */
    #[param_clientside_regex('^[a-zA-Z0-9]*$')]
    case ALPHANUM = 'alphanum';

    /**
     * PARAM_ALPHANUMEXT - expected numbers 0-9, letters (English ascii letters [a-zA-Z]) and _- only.
     */
    #[param_clientside_regex('^[a-zA-Z0-9_\-]*$')]
    case ALPHANUMEXT = 'alphanumext';

    /**
     * PARAM_AUTH - actually checks to make sure the string is a valid auth plugin
     */
    case AUTH = 'auth';

    /**
     * PARAM_BASE64 - Base 64 encoded format
     */
    case BASE64 = 'base64';

    /**
     * PARAM_BOOL - converts input into 0 or 1, use for switches in forms and urls.
     */
    case BOOL = 'bool';

    /**
     * PARAM_CAPABILITY - A capability name, like 'moodle/role:manage'. Actually
     * checked against the list of capabilities in the database.
     */
    case CAPABILITY = 'capability';

    /**
     * PARAM_CLEANHTML - cleans submitted HTML code. Note that you almost never want
     * to use this. The normal mode of operation is to use PARAM_RAW when receiving
     * the input (required/optional_param or formslib) and then sanitise the HTML
     * using format_text on output. This is for the rare cases when you want to
     * sanitise the HTML on input. This cleaning may also fix xhtml strictness.
     */
    case CLEANHTML = 'cleanhtml';

    /**
     * PARAM_EMAIL - an email address following the RFC
     */
    case EMAIL = 'email';

    /**
     * PARAM_FILE - safe file name, all dangerous chars are stripped, protects against XSS, SQL injections and directory traversals
     */
    case FILE = 'file';

    /**
     * PARAM_FLOAT - a real/floating point number.
     *
     * Note that you should not use PARAM_FLOAT for numbers typed in by the user.
     * It does not work for languages that use , as a decimal separator.
     * Use PARAM_LOCALISEDFLOAT instead.
     */
    case FLOAT = 'float';

    /**
     * PARAM_LOCALISEDFLOAT - a localised real/floating point number.
     * This is preferred over PARAM_FLOAT for numbers typed in by the user.
     * Cleans localised numbers to computer readable numbers; false for invalid numbers.
     */
    #[param_clientside_regex('^\d*([\.,])\d+$')]
    case LOCALISEDFLOAT = 'localisedfloat';

    /**
     * PARAM_HOST - expected fully qualified domain name (FQDN) or an IPv4 dotted quad (IP address)
     */
    case HOST = 'host';

    /**
     * PARAM_INT - integers only, use when expecting only numbers.
     */
    case INT = 'int';

    /**
     * PARAM_LANG - checks to see if the string is a valid installed language in the current site.
     */
    case LANG = 'lang';

    /**
     * PARAM_LOCALURL - expected properly formatted URL as well as one that refers to the local server itself. (NOT orthogonal to the
     * others! Implies PARAM_URL!)
     */
    case LOCALURL = 'localurl';

    /**
     * PARAM_NOTAGS - all html tags are stripped from the text. Do not abuse this type.
     */
    case NOTAGS = 'notags';

    /**
     * PARAM_PATH - safe relative path name, all dangerous chars are stripped, protects against XSS, SQL injections and directory
     * traversals note: the leading slash is not removed, window drive letter is not allowed
     */
    case PATH = 'path';

    /**
     * PARAM_PEM - Privacy Enhanced Mail format
     */
    case PEM = 'pem';

    /**
     * PARAM_PERMISSION - A permission, one of CAP_INHERIT, CAP_ALLOW, CAP_PREVENT or CAP_PROHIBIT.
     */
    case PERMISSION = 'permission';

    /**
     * PARAM_RAW specifies a parameter that is not cleaned/processed in any way except the discarding of the invalid utf-8 characters
     */
    case RAW = 'raw';

    /**
     * PARAM_RAW_TRIMMED like PARAM_RAW but leading and trailing whitespace is stripped.
     */
    case RAW_TRIMMED = 'raw_trimmed';

    /**
     * PARAM_SAFEDIR - safe directory name, suitable for include() and require()
     */
    #[param_clientside_regex('^[a-zA-Z0-9_\-]*$')]
    case SAFEDIR = 'safedir';

    /**
     * PARAM_SAFEPATH - several PARAM_SAFEDIR joined by "/", suitable for include() and require(), plugin paths
     * and other references to Moodle code files.
     *
     * This is NOT intended to be used for absolute paths or any user uploaded files.
     */
    #[param_clientside_regex('^[a-zA-Z0-9\/_\-]*$')]
    case SAFEPATH = 'safepath';

    /**
     * PARAM_SEQUENCE - expects a sequence of numbers like 8 to 1,5,6,4,6,8,9.  Numbers and comma only.
     */
    #[param_clientside_regex('^[0-9,]*$')]
    case SEQUENCE = 'sequence';

    /**
     * PARAM_TAG - one tag (interests, blogs, etc.) - mostly international characters and space, <> not supported
     */
    case TAG = 'tag';

    /**
     * PARAM_TAGLIST - list of tags separated by commas (interests, blogs, etc.)
     */
    case TAGLIST = 'taglist';

    /**
     * PARAM_TEXT - general plain text compatible with multilang filter, no other html tags. Please note '<', or '>' are allowed here.
     */
    case TEXT = 'text';

    /**
     * PARAM_THEME - Checks to see if the string is a valid theme name in the current site
     */
    case THEME = 'theme';

    /**
     * PARAM_URL - expected properly formatted URL. Please note that domain part is required, http://localhost/ is not accepted but
     * http://localhost.localdomain/ is ok.
     */
    case URL = 'url';

    /**
     * PARAM_USERNAME - Clean username to only contains allowed characters. This is to be used ONLY when manually creating user
     * accounts, do NOT use when syncing with external systems!!
     */
    case USERNAME = 'username';

    /**
     * PARAM_STRINGID - used to check if the given string is valid string identifier for get_string()
     */
    case STRINGID = 'stringid';

    /**
     * PARAM_CLEAN - obsoleted, please use a more specific type of parameter.
     * It was one of the first types, that is why it is abused so much ;-)
     * @deprecated since 2.0
     */
    #[deprecated(
        replacement: 'a more specific type of parameter',
        since: '2.0',
        reason: 'The CLEAN param type is too generic to perform satisfactory validation',
        emit: false,
    )]
    case CLEAN = 'clean';

    /**
     * PARAM_INTEGER - deprecated alias for PARAM_INT
     * @deprecated since 2.0
     */
    #[deprecated(
        replacement: 'param::INT',
        since: '2.0',
        reason: 'Alias for INT',
        final: true,
    )]
    case INTEGER = 'integer';

    /**
     * PARAM_NUMBER - deprecated alias of PARAM_FLOAT
     * @deprecated since 2.0
     */
    #[deprecated(
        replacement: 'param::FLOAT',
        since: '2.0',
        reason: 'Alias for FLOAT',
        final: true,
    )]
    case NUMBER = 'number';

    /**
     * PARAM_ACTION - deprecated alias for PARAM_ALPHANUMEXT, use for various actions in forms and urls
     * NOTE: originally alias for PARAM_ALPHANUMEXT
     * @deprecated since 2.0
     */
    #[deprecated(
        replacement: 'param::ALPHANUMEXT',
        since: '2.0',
        reason: 'Alias for PARAM_ALPHANUMEXT',
        final: true,
    )]
    case ACTION = 'action';

    /**
     * PARAM_FORMAT - deprecated alias for PARAM_ALPHANUMEXT, use for names of plugins, formats, etc.
     * NOTE: originally alias for PARAM_APLHA
     * @deprecated since 2.0
     */
    #[deprecated(
        replacement: 'param::ALPHANUMEXT',
        since: '2.0',
        reason: 'Alias for PARAM_ALPHANUMEXT',
        final: true,
    )]
    case FORMAT = 'format';

    /**
     * PARAM_MULTILANG - deprecated alias of PARAM_TEXT.
     * @deprecated since 2.0
     */
    #[deprecated(
        replacement: 'param::TEXT',
        since: '2.0',
        reason: 'Alias for PARAM_TEXT',
        final: true,
    )]
    case MULTILANG = 'multilang';

    /**
     * PARAM_TIMEZONE - expected timezone. Timezone can be int +-(0-13) or float +-(0.5-12.5) or
     * string separated by '/' and can have '-' &/ '_' (eg. America/North_Dakota/New_Salem
     * America/Port-au-Prince)
     */
    case TIMEZONE = 'timezone';

    /**
     * PARAM_CLEANFILE - deprecated alias of PARAM_FILE; originally was removing regional chars too
     * @deprecated since 2.0
     */
    #[deprecated(
        replacement: 'param::FILE',
        since: '2.0',
        reason: 'Alias for PARAM_FILE',
    )]
    case CLEANFILE = 'cleanfile';

    /**
     * PARAM_COMPONENT is used for full component names (aka frankenstyle) such as 'mod_forum = 'core_rating', 'auth_ldap'.
     * Short legacy subsystem names and module names are accepted too ex: 'forum = 'rating', 'user'.
     * Only lowercase ascii letters, numbers and underscores are allowed, it has to start with a letter.
     * NOTE: numbers and underscores are strongly discouraged in plugin names!
     */
    #[param_clientside_regex('^[a-z][a-z0-9]*(_(?:[a-z][a-z0-9_](?!__))*)?[a-z0-9]+$')]
    case COMPONENT = 'component';

    /**
     * PARAM_AREA is a name of area used when addressing files, comments, ratings, etc.
     * It is usually used together with context id and component.
     * Only lowercase ascii letters, numbers and underscores are allowed, it has to start with a letter.
     */
    #[param_clientside_regex('^[a-z](?:[a-z0-9_](?!__))*[a-z0-9]+$')]
    case AREA = 'area';

    /**
     * PARAM_PLUGIN is used for plugin names such as 'forum = 'glossary', 'ldap', 'paypal', 'completionstatus'.
     * Only lowercase ascii letters, numbers and underscores are allowed, it has to start with a letter.
     * NOTE: numbers and underscores are strongly discouraged in plugin names! Underscores are forbidden in module names.
     */
    #[param_clientside_regex('^[a-z](?:[a-z0-9_](?!__))*[a-z0-9]+$')]
    case PLUGIN = 'plugin';

    /**
     * Get the canonical enumerated parameter from the parameter type name.
     *
     * @param string $paramname
     * @return param
     * @throws coding_exception If the parameter is unknown.
     */
    public static function from_type(string $paramname): self {
        $from = self::tryFrom($paramname)?->canonical();
        if ($from) {
            return $from;
        }

        throw new \coding_exception("Unknown parameter type '{$paramname}'");
    }

    /**
     * Canonicalise the parameter.
     *
     * This method is used to support aliasing of deprecated parameters.
     *
     * @return param
     */
    private function canonical(): self {
        return match ($this) {
            self::ACTION => self::ALPHANUMEXT,
            self::CLEANFILE => self::FILE,
            self::FORMAT => self::ALPHANUMEXT,
            self::INTEGER => self::INT,
            self::MULTILANG => self::TEXT,
            self::NUMBER => self::FLOAT,

            default => $this,
        };
    }

    /**
     * Used by {@link optional_param()} and {@link required_param()} to
     * clean the variables and/or cast to specific types, based on
     * an options field.
     *
     * <code>
     * $course->format = param::ALPHA->clean($course->format);
     * $selectedgradeitem = param::INT->clean($selectedgradeitem);
     * </code>
     *
     * @param mixed $value the value to clean
     * @return mixed
     * @throws coding_exception
     */
    public function clean(mixed $value): mixed {
        // Check and emit a deprecation notice if required.
        deprecation::emit_deprecation_if_present($this);

        if (is_array($value)) {
            throw new coding_exception('clean() can not process arrays, please use clean_array() instead.');
        } else if (is_object($value)) {
            if (method_exists($value, '__toString')) {
                $value = $value->__toString();
            } else {
                throw new coding_exception('clean() can not process objects, please use clean_array() instead.');
            }
        }

        $canonical = $this->canonical();
        if ($this !== $canonical) {
            return $canonical->clean($value);
        }

        $methodname = "clean_param_value_{$this->value}";
        if (!method_exists(self::class, $methodname)) {
            throw new coding_exception("Method not found for cleaning {$this->value}");
        }

        return $this->{$methodname}($value);
    }

    /**
     * Get the clientside regular expression for this parameter.
     *
     * @return null|string
     */
    public function get_clientside_expression(): ?string {
        $ref = new \ReflectionClassConstant(self::class, $this->name);
        $attributes = $ref->getAttributes(param_clientside_regex::class);
        if (count($attributes) === 0) {
            return null;
        }

        return $attributes[0]->newInstance()->regex;
    }

    /**
     * Returns a value for the named variable, taken from request arguments.
     *
     * This function should be used to initialise all required values
     * in a script that are based on parameters.  Usually it will be
     * used like this:
     *    $id = param::INT->required_param('id');
     *
     *
     * @param string $paramname the name of the page parameter we want
     * @return mixed
     * @throws moodle_exception
     */
    public function required_param(string $paramname): mixed {
        return $this->clean($this->get_request_parameter($paramname, true));
    }

    /**
     * Returns a particular array value for the named variable, taken from request arguments.
     * If the parameter doesn't exist then an error is thrown because we require this variable.
     *
     * This function should be used to initialise all required values
     * in a script that are based on parameters.  Usually it will be
     * used like this:
     *    $ids = required_param_array('ids', PARAM_INT);
     *
     * Note: arrays of arrays are not supported, only alphanumeric keys with _ and - are supported
     *
     * @param string $paramname the name of the page parameter we want
     * @return array
     * @throws moodle_exception
     */
    public function required_param_array(string $paramname): array {
        $param = $this->get_request_parameter($paramname, true);

        if (!is_array($param)) {
            throw new \moodle_exception('missingparam', '', '', $paramname);
        }

        $result = [];
        foreach ($param as $key => $value) {
            if (!preg_match('/^[a-z0-9_-]+$/i', $key)) {
                debugging(
                    "Invalid key name in required_param_array() detected: {$key}, parameter: {$paramname}",
                );
                continue;
            }
            $result[$key] = $this->clean($value);
        }

        return $result;
    }

    /**
     * Returns a particular value for the named variable from the request arguments,
     * otherwise returning a given default.
     *
     * This function should be used to initialise all optional values
     * in a script that are based on parameters.  Usually it will be
     * used like this:
     *    $name = param::TEXT->optional_param('name', 'Fred');
     *
     * @param string $paramname the name of the page parameter we want
     * @param mixed  $default the default value to return if nothing is found
     * @return mixed
     */
    public function optional_param(string $paramname, mixed $default): mixed {
        $param = $this->get_request_parameter($paramname, false);
        if ($param === null) {
            return $default;
        }

        return $this->clean($param);
    }

    /**
     * Returns a particular array value for the named variable from the request arguments,
     * otherwise returning a given default.
     *
     * This function should be used to initialise all optional values
     * in a script that are based on parameters.  Usually it will be
     * used like this:
     *    $ids = param::INT->optional_param_arrayt('id', array());
     *
     * Note: arrays of arrays are not supported, only alphanumeric keys with _ and - are supported.
     *
     * @param string $paramname the name of the page parameter we want
     * @param mixed $default the default value to return if nothing is found
     * @return array
     */
    public function optional_param_array(string $paramname, mixed $default): mixed {
        $param = $this->get_request_parameter($paramname, false);
        if ($param === null) {
            return $default;
        }

        if (!is_array($param)) {
            debugging(
                "optional_param_array() only expects array parameters: {$paramname}",
            );
            return $default;
        }

        $result = [];
        foreach ($param as $key => $value) {
            if (!preg_match('/^[a-z0-9_-]+$/i', $key)) {
                debugging(
                    "Invalid key name in optional_param_array() detected: {$key}, parameter: {$paramname}",
                );
                continue;
            }
            $result[$key] = $this->clean($value);
        }

        return $result;
    }

    /**
     * Returns a particular value for the named variable, taken from the POST, or GET params.
     *
     * Parameters are fetched from POST first, then GET.
     *
     * @param string $paramname
     * @param bool $require
     * @return mixed
     * @throws moodle_exception If the parameter was not found and the value is required
     */
    private function get_request_parameter(
        string $paramname,
        bool $require,
    ): mixed {
        if (isset($_POST[$paramname])) {
            return $_POST[$paramname];
        } else if (isset($_GET[$paramname])) {
            return $_GET[$paramname];
        } else if ($require) {
            throw new \moodle_exception('missingparam', '', '', $paramname);
        }

        return null;
    }

    /**
     * Strict validation of parameter values, the values are only converted
     * to requested PHP type. Internally it is using clean_param, the values
     * before and after cleaning must be equal - otherwise
     * an invalid_parameter_exception is thrown.
     * Objects and classes are not accepted.
     *
     * @param mixed $param
     * @param bool $allownull are nulls valid value?
     * @param string $debuginfo optional debug information
     * @return mixed the $param value converted to PHP type
     * @throws invalid_parameter_exception if $param is not of given type
     */
    public function validate_param(
        mixed $param,
        bool $allownull = NULL_NOT_ALLOWED,
        string $debuginfo = '',
    ): mixed {
        if (is_null($param)) {
            if ($allownull == NULL_ALLOWED) {
                return null;
            } else {
                throw new invalid_parameter_exception($debuginfo);
            }
        }
        if (is_array($param) || is_object($param)) {
            throw new invalid_parameter_exception($debuginfo);
        }

        $cleaned = $this->clean($param);

        if ($this->canonical() === self::FLOAT) {
            // Do not detect precision loss here.
            if (is_float($param) || is_int($param)) {
                // These always fit.
            } else if (!is_numeric($param) || !preg_match('/^[\+-]?[0-9]*\.?[0-9]*(e[-+]?[0-9]+)?$/i', (string)$param)) {
                throw new invalid_parameter_exception($debuginfo);
            }
        } else if ((string) $param !== (string) $cleaned) {
            // Conversion to string is usually lossless.
            throw new invalid_parameter_exception($debuginfo);
        }

        return $cleaned;
    }

    /**
     * Makes sure array contains only the allowed types, this function does not validate array key names!
     *
     * <code>
     * $options = param::INT->clean_param_array($options);
     * </code>
     *
     * @param array|null $param the variable array we are cleaning
     * @param bool $recursive clean recursive arrays
     * @return array
     * @throws coding_exception
     */
    public function clean_param_array(
        ?array $param,
        bool $recursive = false,
    ) {
        // Convert null to empty array.
        $param = (array) $param;
        foreach ($param as $key => $value) {
            if (is_array($value)) {
                if ($recursive) {
                    $param[$key] = $this->clean_param_array($value, true);
                } else {
                    throw new coding_exception('clean_param_array can not process multidimensional arrays when $recursive is false.');
                }
            } else {
                $param[$key] = $this->clean($value);
            }
        }
        return $param;
    }

    /**
     * Validation for PARAM_RAW.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_raw(mixed $param): mixed {
        // No cleaning at all.
        return fix_utf8($param);
    }

    /**
     * Validation for PARAM_RAW_TRIMMED.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_raw_trimmed(mixed $param): string {
        // No cleaning, but strip leading and trailing whitespace.
        return trim((string) $this->clean_param_value_raw($param));
    }

    /**
     * Validation for PARAM_CLEAN.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_clean(mixed $param): string {
        // General HTML cleaning, try to use more specific type if possible this is deprecated!
        // Please use more specific type instead.
        if (is_numeric($param)) {
            return $param;
        }
        $param = fix_utf8($param);
        // Sweep for scripts, etc.
        return clean_text($param);
    }

    /**
     * Validation for PARAM_CLEANHTML.
     */
    protected function clean_param_value_cleanhtml(mixed $param): mixed {
        // Clean html fragment.
        $param = (string)fix_utf8($param);
        // Sweep for scripts, etc.
        $param = clean_text($param, FORMAT_HTML);

        return trim($param);
    }

    /**
     * Validation for PARAM_INT.
     *
     * @param mixed $param
     * @return int
     */
    protected function clean_param_value_int(mixed $param): int {
        // Convert to integer.
        return (int)$param;
    }

    /**
     * Validation for PARAM_FLOAT.
     *
     * @param mixed $param
     * @return float
     */
    protected function clean_param_value_float(mixed $param): float {
        // Convert to float.
        return (float)$param;
    }

    /**
     * Validation for PARAM_LOCALISEDFLOAT.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_localisedfloat(mixed $param): mixed {
        // Convert to float.
        return unformat_float($param, true);
    }

    /**
     * Validation for PARAM_ALPHA.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_alpha(mixed $param): mixed {
        // Remove everything not `a-z`.
        return preg_replace('/[^a-zA-Z]/i', '', (string)$param);
    }

    /**
     * Validation for PARAM_ALPHAEXT.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_alphaext(mixed $param): mixed {
        // Remove everything not `a-zA-Z_-` (originally allowed "/" too).
        return preg_replace('/[^a-zA-Z_-]/i', '', (string)$param);
    }

    /**
     * Validation for PARAM_ALPHANUM.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_alphanum(mixed $param): mixed {
        // Remove everything not `a-zA-Z0-9`.
        return preg_replace('/[^A-Za-z0-9]/i', '', (string)$param);
    }

    /**
     * Validation for PARAM_ALPHANUMEXT.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_alphanumext(mixed $param): mixed {
        // Remove everything not `a-zA-Z0-9_-`.
        return preg_replace('/[^A-Za-z0-9_-]/i', '', (string)$param);
    }

    /**
     * Validation for PARAM_SEQUENCE.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_sequence(mixed $param): mixed {
        // Remove everything not `0-9,`.
        return preg_replace('/[^0-9,]/i', '', (string)$param);
    }

    /**
     * Validation for PARAM_BOOL.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_bool(mixed $param): mixed {
        // Convert to 1 or 0.
        $tempstr = strtolower((string)$param);
        if ($tempstr === 'on' || $tempstr === 'yes' || $tempstr === 'true') {
            $param = 1;
        } else if ($tempstr === 'off' || $tempstr === 'no' || $tempstr === 'false') {
            $param = 0;
        } else {
            $param = empty($param) ? 0 : 1;
        }
        return $param;
    }

    /**
     * Validation for PARAM_NOTAGS.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_notags(mixed $param): mixed {
        // Strip all tags.
        $param = fix_utf8($param);
        return strip_tags((string)$param);
    }

    /**
     * Validation for PARAM_TEXT.
     *
     * @param mixed $param
     * @return mixed
     */
    protected function clean_param_value_text(mixed $param): mixed {
        // Leave only tags needed for multilang.
        $param = fix_utf8($param);
        // If the multilang syntax is not correct we strip all tags because it would break xhtml strict which is required
        // for accessibility standards please note this cleaning does not strip unbalanced '>' for BC compatibility reasons.
        do {
            if (strpos((string)$param, '</lang>') !== false) {
                // Old and future mutilang syntax.
                $param = strip_tags($param, '<lang>');
                if (!preg_match_all('/<.*>/suU', $param, $matches)) {
                    break;
                }
                $open = false;
                foreach ($matches[0] as $match) {
                    if ($match === '</lang>') {
                        if ($open) {
                            $open = false;
                            continue;
                        } else {
                            break 2;
                        }
                    }
                    if (!preg_match('/^<lang lang="[a-zA-Z0-9_-]+"\s*>$/u', $match)) {
                        break 2;
                    } else {
                        $open = true;
                    }
                }
                if ($open) {
                    break;
                }
                return $param;
            } else if (strpos((string)$param, '</span>') !== false) {
                // Current problematic multilang syntax.
                $param = strip_tags($param, '<span>');
                if (!preg_match_all('/<.*>/suU', $param, $matches)) {
                    break;
                }
                $open = false;
                foreach ($matches[0] as $match) {
                    if ($match === '</span>') {
                        if ($open) {
                            $open = false;
                            continue;
                        } else {
                            break 2;
                        }
                    }
                    if (!preg_match('/^<span(\s+lang="[a-zA-Z0-9_-]+"|\s+class="multilang"){2}\s*>$/u', $match)) {
                        break 2;
                    } else {
                        $open = true;
                    }
                }
                if ($open) {
                    break;
                }
                return $param;
            }
        } while (false);
        // Easy, just strip all tags, if we ever want to fix orphaned '&' we have to do that in format_string().
        return strip_tags((string)$param);
    }

    /**
     * Validation for PARAM_COMPONENT.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_component(mixed $param): string {
        // We do not want any guessing here, either the name is correct or not
        // please note only normalised component names are accepted.
        $param = (string)$param;
        if (!preg_match('/^[a-z][a-z0-9]*(_[a-z][a-z0-9_]*)?[a-z0-9]+$/', $param)) {
            return '';
        }
        if (strpos($param, '__') !== false) {
            return '';
        }
        if (strpos($param, 'mod_') === 0) {
            // Module names must not contain underscores because we need to differentiate them from invalid plugin types.
            if (substr_count($param, '_') != 1) {
                return '';
            }
        }
        return $param;
    }

    /**
     * Validation for PARAM_PLUGIN.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_plugin(mixed $param): string {
        return $this->clean_param_value_area($param);
    }

    /**
     * Validation for PARAM_AREA.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_area(mixed $param): string {
        // We do not want any guessing here, either the name is correct or not.
        if (!is_valid_plugin_name($param)) {
            return '';
        }
        return $param;
    }

    /**
     * Validation for PARAM_SAFEDIR
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_safedir(mixed $param): string {
        // Remove everything not a-zA-Z0-9_- .
        return preg_replace('/[^a-zA-Z0-9_-]/i', '', (string)$param);
    }

    /**
     * Validation for PARAM_SAFEPATH.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_safepath(mixed $param): string {
        // Remove everything not a-zA-Z0-9/_- .
        return preg_replace('/[^a-zA-Z0-9\/_-]/i', '', (string)$param);
    }

    /**
     * Validation for PARAM_FILE.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_file(mixed $param): string {
        // Strip all suspicious characters from filename.
        $param = (string)fix_utf8($param);
        // phpcs:ignore moodle.Strings.ForbiddenStrings.Found
        $param = preg_replace('~[[:cntrl:]]|[&<>"`\|\':\\\\/]~u', '', $param);
        if ($param === '.' || $param === '..') {
            $param = '';
        }
        return $param;
    }

    /**
     * Validation for PARAM_PATH.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_path(mixed $param): string {
        // Strip all suspicious characters from file path.
        $param = (string)fix_utf8($param);
        $param = str_replace('\\', '/', $param);

        // Explode the path and clean each element using the PARAM_FILE rules.
        $breadcrumb = explode('/', $param);
        foreach ($breadcrumb as $key => $crumb) {
            if ($crumb === '.' && $key === 0) {
                // Special condition to allow for relative current path such as ./currentdirfile.txt.
            } else {
                $crumb = clean_param($crumb, PARAM_FILE);
            }
            $breadcrumb[$key] = $crumb;
        }
        $param = implode('/', $breadcrumb);

        // Remove multiple current path (./././) and multiple slashes (///).
        $param = preg_replace('~//+~', '/', $param);
        $param = preg_replace('~/(\./)+~', '/', $param);
        return $param;
    }

    /**
     * Validation for PARAM_HOST.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_host(mixed $param): string {
        // Allow FQDN or IPv4 dotted quad.
        if (!ip_utils::is_domain_name($param) && !ip_utils::is_ipv4_address($param)) {
            $param = '';
        }
        return $param;
    }

    /**
     * Validation for PARAM_URL.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_url(mixed $param): string {
        global $CFG;

        // Allow safe urls.
        $param = (string)fix_utf8($param);
        include_once($CFG->dirroot . '/lib/validateurlsyntax.php');
        if (!empty($param) && validateUrlSyntax($param, 's?H?S?F?E-u-P-a?I?p?f?q?r?')) {
            // All is ok, param is respected.
        } else {
            // Not really ok.
            $param = '';
        }
        return $param;
    }

    /**
     * Validation for PARAM_LOCALURL.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_localurl(mixed $param): string {
        global $CFG;

        // Allow http absolute, root relative and relative URLs within wwwroot.
        $param = clean_param($param, PARAM_URL);
        if (!empty($param)) {
            if ($param === $CFG->wwwroot) {
                // Exact match.
            } else if (preg_match(':^/:', $param)) {
                // Root-relative, ok!
            } else if (preg_match('/^' . preg_quote($CFG->wwwroot . '/', '/') . '/i', $param)) {
                // Absolute, and matches our wwwroot.
            } else {
                // Relative - let's make sure there are no tricks.
                if (validateUrlSyntax('/' . $param, 's-u-P-a-p-f+q?r?') &&
                        !preg_match('/javascript(?:.*\/{2,})?:/i', rawurldecode($param))) {
                    // Valid relative local URL.
                } else {
                    $param = '';
                }
            }
        }
        return $param;
    }

    /**
     * Validation for PARAM_PEM.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_pem(mixed $param): string {
        $param = trim((string)$param);
        // PEM formatted strings may contain letters/numbers and the symbols:
        // - forward slash: /
        // - plus sign:     +
        // - equal sign:    =
        // - , surrounded by BEGIN and END CERTIFICATE prefix and suffixes.
        if (preg_match('/^-----BEGIN CERTIFICATE-----([\s\w\/\+=]+)-----END CERTIFICATE-----$/', trim($param), $matches)) {
            [$wholething, $body] = $matches;
            unset($wholething, $matches);
            $b64 = clean_param($body, PARAM_BASE64);
            if (!empty($b64)) {
                return "-----BEGIN CERTIFICATE-----\n$b64\n-----END CERTIFICATE-----\n";
            } else {
                return '';
            }
        }
        return '';
    }

    /**
     * Validation for PARAM_BASE64.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_base64(mixed $param): string {
        if (!empty($param)) {
            // PEM formatted strings may contain letters/numbers and the symbols
            // - forward slash: /
            // - plus sign:     +
            // - equal sign:    =.
            if (0 >= preg_match('/^([\s\w\/\+=]+)$/', trim($param))) {
                return '';
            }
            $lines = preg_split('/[\s]+/', $param, -1, PREG_SPLIT_NO_EMPTY);
            // Each line of base64 encoded data must be 64 characters in length, except for the last line which may be less
            // than (or equal to) 64 characters long.
            for ($i = 0, $j = count($lines); $i < $j; $i++) {
                if ($i + 1 == $j) {
                    if (64 < strlen($lines[$i])) {
                        return '';
                    }
                    continue;
                }

                if (64 != strlen($lines[$i])) {
                    return '';
                }
            }
            return implode("\n", $lines);
        } else {
            return '';
        }
    }

    /**
     * Validation for PARAM_TAG.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_tag(mixed $param): string {
        $param = (string)fix_utf8($param);
        // Please note it is not safe to use the tag name directly anywhere,
        // it must be processed with s(), urlencode() before embedding anywhere.
        // Remove some nasties.
        // phpcs:ignore moodle.Strings.ForbiddenStrings.Found
        $param = preg_replace('~[[:cntrl:]]|[<>`]~u', '', $param);
        // Convert many whitespace chars into one.
        $param = preg_replace('/\s+/u', ' ', $param);
        $param = core_text::substr(trim($param), 0, TAG_MAX_LENGTH);
        return $param;
    }

    /**
     * Validation for PARAM_TAGLIST.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_taglist(mixed $param): string {
        $param = (string)fix_utf8($param);
        $tags = explode(',', $param);
        $result = [];
        foreach ($tags as $tag) {
            $res = clean_param($tag, PARAM_TAG);
            if ($res !== '') {
                $result[] = $res;
            }
        }
        if ($result) {
            return implode(',', $result);
        } else {
            return '';
        }
    }

    /**
     * Validation for PARAM_CAPABILITY.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_capability(mixed $param): string {
        if (get_capability_info($param)) {
            return $param;
        } else {
            return '';
        }
    }

    /**
     * Validation for PARAM_PERMISSION.
     *
     * @param mixed $param
     * @return int
     */
    protected function clean_param_value_permission(mixed $param): int {
        $param = (int)$param;
        if (in_array($param, [CAP_INHERIT, CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT])) {
            return $param;
        } else {
            return CAP_INHERIT;
        }
    }

    /**
     * Validation for PARAM_AUTH.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_auth(mixed $param): string {
        $param = clean_param($param, PARAM_PLUGIN);
        if (empty($param)) {
            return '';
        } else if (exists_auth_plugin($param)) {
            return $param;
        } else {
            return '';
        }
    }

    /**
     * Validation for PARAM_LANG.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_lang(mixed $param): string {
        $param = clean_param($param, PARAM_SAFEDIR);
        if (get_string_manager()->translation_exists($param)) {
            return $param;
        } else {
            // Specified language is not installed or param malformed.
            return '';
        }
    }

    /**
     * Validation for PARAM_THEME.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_theme(mixed $param): string {
        global $CFG;

        $param = clean_param($param, PARAM_PLUGIN);
        if (empty($param)) {
            return '';
        } else if (file_exists("$CFG->dirroot/theme/$param/config.php")) {
            return $param;
        } else if (!empty($CFG->themedir) && file_exists("$CFG->themedir/$param/config.php")) {
            return $param;
        } else {
            // Specified theme is not installed.
            return '';
        }
    }

    /**
     * Validation for PARAM_USERNAME.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_username(mixed $param): string {
        global $CFG;

        $param = (string)fix_utf8($param);
        $param = trim($param);
        // Convert uppercase to lowercase MDL-16919.
        $param = core_text::strtolower($param);
        if (empty($CFG->extendedusernamechars)) {
            $param = str_replace(" ", "", $param);
            // Regular expression, eliminate all chars EXCEPT:
            // alphanum, dash (-), underscore (_), at sign (@) and period (.) characters.
            $param = preg_replace('/[^-\.@_a-z0-9]/', '', $param);
        }
        return $param;
    }

    /**
     * Validation for PARAM_EMAIL.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_email(mixed $param): string {
        $param = fix_utf8($param);
        if (validate_email($param ?? '')) {
            return $param;
        } else {
            return '';
        }
    }

    /**
     * Validation for PARAM_STRINGID.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_stringid(mixed $param): string {
        if (preg_match('|^[a-zA-Z][a-zA-Z0-9\.:/_-]*$|', (string)$param)) {
            return $param;
        } else {
            return '';
        }
    }

    /**
     * Validation for PARAM_TIMEZONE.
     *
     * @param mixed $param
     * @return string
     */
    protected function clean_param_value_timezone(mixed $param): string {
        // Can be int, float(with .5 or .0) or string seperated by '/' and can have '-_'.
        $param = (string)fix_utf8($param);
        $timezonepattern = '/^(([+-]?(0?[0-9](\.[5|0])?|1[0-3](\.0)?|1[0-2]\.5))|(99)|[[:alnum:]]+(\/?[[:alpha:]_-])+)$/';
        if (preg_match($timezonepattern, $param)) {
            return $param;
        } else {
            return '';
        }
    }

    /**
     * Whether the parameter is deprecated.
     *
     * @return bool
     */
    public function is_deprecated(): bool {
        return deprecation::is_deprecated($this);
    }
}
