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
 * Definition helper to make classes available via webservices.
 *
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace theme_snap\webservice;

defined('MOODLE_INTERNAL') || die();

use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use coding_exception;
use moodle_url;
use cache;

/**
 * Definition helper class.
 * Note: If you are using this class, please be aware that you need to purge caches to force new definitions when
 * classes change. To ensure this happens for a release you can simply version bump your plugin.
 *
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class definition_helper {

    /**
     * @var string
     */
    protected $classname;

    /**
     * @var array
     */
    protected $usenamespaces = [];

    /**
     * @var external_value[];
     */
    protected $definition;

    /**
     * definition_helper constructor.
     * @param string|object $classorobject
     */
    public function __construct($classorobject) {
        $reflect = new \ReflectionClass($classorobject);
        $this->classname = $reflect->getName();

        $cacheddefinition = $this->get_definition_from_cache($this->classname);
        if ($cacheddefinition) {
            $this->definition = $cacheddefinition;
            return;
        }

        $this->set_use_namespaces($classorobject);
        $this->define_class_for_webservice_internal($classorobject);
        $this->cache_definition($this->classname, $this->definition);
    }

    /**
     * Get definition for classname from cache.
     * @param string $classname
     */
    private function get_definition_from_cache($classname) {
        $cache = cache::make('theme_snap', 'webservicedefinitions');
        $data = $cache->get($classname);
        return unserialize($data);
    }

    /**
     * Cache definition for specific classname.
     * @param string $classname
     * @param external_value[] $definition
     */
    private function cache_definition($classname, $definition) {
        $cache = cache::make('theme_snap', 'webservicedefinitions');
        $cache->set($classname, serialize($definition));
    }

    /**
     * Get defintion.
     * @return external_value[]
     */
    public function get_definition() {
        return $this->definition;
    }

    /**
     * Set namespaces used by the class or object.
     * @param $classorobject
     */
    private function set_use_namespaces($classorobject) {
        $reflect = new \ReflectionClass($classorobject);
        $classfilename = $reflect->getFileName();
        if (empty($classfilename)) {
            throw new coding_exception('Failed to get filename for class '.$reflect->getName());
        }
        $contents = file_get_contents($classfilename);
        if (!$contents) {
            return;
        }
        $matches = [];
        preg_match_all('/(?<=use)\s(\S*);/m', $contents, $matches);
        if (count($matches) < 2) {
            return;
        }
        $this->usenamespaces = $matches[1];
    }

    /**
     * Create an external value from an object containing type, description, required properties.
     * @param stdClass $obj
     * @return external_value
     * @throws coding_exception
     */
    private function create_external_value_from_obj($obj) {
        if (empty($obj->type)) {
            throw new coding_exception('Object is missing type property');
        }
        $type = $obj->type;
        $description = !empty($obj->description) ? $obj->description : null;

        if (isset($obj->required)) {
            $required = !empty($obj->required) || $obj->required === false ? $obj->required : null;
            if (!is_bool($required)) {
                // If null, required will be false by default.
                $required = strtolower($required) === 'true';
            }
        } else {
            $required = false;
        }

        if (isset($obj->allownull)) {
            $allownull = !empty($obj->allownull) || $obj->allownull === false ? $obj->allownull : null;
            if (!is_bool($allownull)) {
                // If null, allownull will be true by default.
                $allownull = !strtolower($allownull) === 'false';
            }
        } else {
            $allownull = true;
        }

        $required = $required ? VALUE_REQUIRED : VALUE_OPTIONAL;

        $extval = new external_value($type, $description, $required, null, $allownull);
        return $extval;
    }

    /**
     * Converts an object's 'PARAM_TYPE' string values into constants OR classes into new definitions.
     *
     * @param $obj
     */
    private function convert_object_params($obj) {
        foreach ($obj as $key => $val) {
            if (isset($val->type) && is_string($val->type)) {
                if (defined($val->type)) {
                    $obj->$key->type = constant($val->type);
                    $obj->$key = $this->create_external_value_from_obj($obj->$key);
                } else if (strpos($val->type, '{') !== false) {
                    $obj->$key->type = $this->convert_ws_param_to_object($val->type);
                } else {
                    $classdetected = $this->get_class_from_type($val->type);
                    if ($classdetected) {
                        if ($classdetected === $this->classname) {
                            throw new coding_exception($this->param_error('Class definition infinite recursion ', $key));
                        }
                        $isarray = strpos($val->type, '[]') !== false;
                        if ($isarray) {
                            $defineobj = new definition_helper($classdetected);
                            if (empty($val->description)) {
                                $val->description = '';
                            }
                            $obj->$key = new external_multiple_structure(
                                new external_single_structure((array) $defineobj->get_definition(), $val->description)
                            );
                        } else {
                            $obj->$key = new external_single_structure((array) $defineobj, $val->description);
                        }
                    } else {
                        throw new coding_exception(
                            $this->param_error('Unable to process type '.$val->type, $key)
                        );
                    }
                }
            } else {
                if (is_object($obj->$key)) {
                    $obj->$key = $this->convert_object_params($val);
                } else {
                    throw new coding_exception('Type not specified', var_export($obj, true));
                }
            }
        }
    }

    /**
     * Convert a json style wsparam to an object.
     * NOTE: Will work with relaxed json - e.g.
     * {
     *      description: "My description"
     * };
     * as opposed to
     * {
     *      "description" : "My description"
     * };
     * @param $comment
     * @return [$obj, $isarray]
     */
    private function convert_ws_param_to_object($comment) {
        $matches = [];
        $regex = '/(?<=@wsparam)(?:\s*{)(.*)(};|}\[\];)/s';

        $haswsparamdoc = preg_match($regex, $comment, $matches);
        if ($haswsparamdoc !== 1) {
            return ['object' => false, 'isarray' => false];
        }
        $isarray = strpos($matches[2], '[]') !== false;

        $result = $matches[1];
        // Remove comment asterixes.
        $result = '{' . "\n" . preg_replace('/(^\s*\*)/m', "", $result) . '}';

        // Make sure keys have strings round them.
        $regex = '/^\s*([^":{}]*):/m';
        $result = preg_replace_callback($regex, function($matches) {
            $str = trim($matches[1]);
            $str = '"' . $str . '"';
            return str_replace($matches[1], $str, $matches[0]);
        }, $result);

        // Make sure values have strings round them, where appropriate.
        $regex = '/(?<=:)([^"{},]*)(?:,|$)/m';
        $result = preg_replace_callback($regex, function($matches) {
            $str = trim($matches[1]);
            // Exclude numbers.
            if (strval(floatval($str)) === $str) {
                return $matches[0];
            }
            // Exclude true / false.
            $excs = ['true', 'false'];
            foreach ($excs as $exc) {
                if (strtolower($str) === $exc) {
                    return $matches[0];
                }
            }
            $str = '"' . $str . '"';
            return str_replace($matches[1], $str, $matches[0]);
        }, $result);

        $obj = json_decode($result);
        $this->convert_object_params($obj);
        return [$obj, $isarray];
    }

    /**
     * @param string $errmsg - error message.
     * @param string $name - property name.
     * @return string
     */
    private function param_error($errmsg, $name) {
        if (substr($errmsg, -1) !== ' ') {
            $errmsg .= ' ';
        }
        return $errmsg . 'for ' . $name . ' in class ' . $this->classname;
    }

    /**
     * Defines a property from a @wsparam
     * "@wsparam" is intended for documenting stdClass via json.
     * @param $comment
     * @return bool
     * @throws coding_exception
     */
    private function define_from_ws_param($name, $comment) {
        $matches = [];
        $regex = '/(?<=\*\s@wsparam\s)(\S*)\s(.*)/';
        $haswsparamdoc = preg_match($regex, $comment, $matches);
        if ($haswsparamdoc !== 1) {
            return false;
        }

        if (strpos($comment, '{') === false) {
            throw new coding_exception($this->param_error('@wsparam not defined with json', $name));
        }

        // The comment was formatted as json.
        list ($obj, $isarray) = $this->convert_ws_param_to_object($comment);
        if (!$obj) {
            throw new coding_exception($this->param_error('Failed to decode @wsparam json', $name));
        }

        if (isset($obj->type) && is_string($obj->type)) {
            // @codingStandardsIgnoreStart
            // The comment is defining just this parameter - e.g:.
            /**
             * @wsparam {
             *    type: PARAM_INT,
             *    description: "Counter",
             *    required: true
             * };
             */
            // @codingStandardsIgnoreEnd
            if (empty($obj->description)) {
                throw new coding_exception($this->param_error('Missing description for', $name));
            }

            $required = !empty($obj->required) ? VALUE_REQUIRED : VALUE_OPTIONAL;
            return new external_value($obj->type, $obj->description, $required, !empty($obj->required) ? 0 : null);
        } else {
            // @codingStandardsIgnoreStart
            // The comment is defining an object - i.e. StdClass - e.g:.
            /**
             * @wsparam {
             *    progress: {
             *        type: PARAM_INT,
             *        description: "Student progress",
             *        required: true
             *    },
             *    total: {
             *        type: PARAM_INT,
             *        description: "Total to complete"
             *    }
             * };
             */
            // @codingStandardsIgnoreEnd
            if ($isarray) {
                return new external_multiple_structure(new external_single_structure((array)$obj));
            } else {
                return new external_single_structure((array)$obj);
            }
        }
    }

    /**
     * Get a single line @ws doc entry from comment - @wstype, @wsdesc, @wsrequired, @wsallownull (excludes @wsparam).
     * @param string|array $key
     * @return string | false;
     */
    private function get_wsdoc($comment, $key) {
        if (is_array($key)) {
            $keys = $key;
        } else {
            $keys = [$key];
        }
        foreach ($keys as $key) {
            if ($key === '@wsallownull' || $key === '@wsrequired') {
                // For allownull / required, we need to check for just an empty doc and accept that as true.
                $regex = '/\s*\*\s@wsrequired(\n|$)/';
                if (preg_match($regex, $comment) === 1) {
                    return true;
                }
            }
            $regex = '/(?<=\*\s'.$key.')\s*(.*)(?:\n|$)/';
            $hasdoc = preg_match($regex, $comment, $matches);
            $val = $hasdoc && count($matches) > 1 ? $matches[1] : false;
            if ($val) {
                if ($key === '@wsallownull' || $key === '@wsrequired') {
                    if (strtolower(trim($val)) === 'true' || trim($val) === '1') {
                        return true;
                    } else {
                        return false;
                    }
                }
                return $val;
            }
        }
        if ($key === '@wsallownull') {
            return true; // Failed to find but return default value of true.
        }
        return false; // Failed to find.
    }

    /**
     * Get class from type.
     * @param string $type
     * @return bool|mixed|string
     */
    private function get_class_from_type($type) {
        $reflect = new \ReflectionClass($this->classname);
        $chktype = str_replace('[]', '', $type);
        $mainnamespace = $reflect->getNamespaceName();

        if (class_exists($chktype)) {
            return $chktype;
        } else if (class_exists($mainnamespace . '\\' . $chktype)) {
            return $mainnamespace . '\\' . $chktype;
        } else {
            foreach ($this->usenamespaces as $namespace) {
                $regex = '/\\\\' . $chktype . '$/';
                if (preg_match($regex, $namespace) === 1) {
                    if (class_exists($namespace)) {
                        return $namespace;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Returns an array of external_value objects based on a class or object for use with defining a webservice.
     *
     * @param $classorobject
     * @throws coding_exception
     */
    private function define_class_for_webservice_internal($classorobject) {
        $reflect = new \ReflectionClass($classorobject);
        $public = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        $this->definition = [];
        foreach ($public as $property) {
            $name = $property->getName();
            $comment = $property->getDocComment();

            $classdetected = '';
            $isarray = false;

            // Do we have a @wsparam to define this property?
            $regex = '/(?<=\*\s@wsparam\s)(\S*)\s(.*)/';
            $haswsparamdoc = preg_match($regex, $comment);
            if ($haswsparamdoc === 1) {
                $this->definition[$name] = $this->define_from_ws_param($name, $comment);
                continue;
            }

            // Do we have a @wstype to define the type of this property?
            $wstype = $this->get_wsdoc($comment, '@wstype');
            if ($wstype) {
                $wstype = constant($wstype);
            }

            // Do we have a @wsdescription / @wsdesc to define the description of this property?
            $wsdesc = $this->get_wsdoc($comment, ['@wsdescription', '@wsdesc']);

            // Do we have a @wsrequired to define the required status of this property?
            $wsreq = $this->get_wsdoc($comment, ['@wsrequired']);
            $wsreq = $wsreq ? VALUE_REQUIRED : VALUE_OPTIONAL;

            // Do we have a @wsallownull?
            $wsallownull = $this->get_wsdoc($comment, ['@wsallownull']);

            // Get information from @var doc.
            $regex = '/(?<=\*\s@var\s)(\S*)([^\S\x0a\x0d].*|)/m';
            $matches = [];
            $hasvardoc = preg_match($regex, $comment, $matches);
            if ($hasvardoc !== 1 && (empty($wstype) && empty($wsdesc))) {
                throw new coding_exception('Property '.$name.' missing @var or @wsparam doc in class '.$this->classname);
            }

            // Establish description.
            if (!empty($wsdesc)) {
                // Use @wsdesc.
                $description = $wsdesc;
            } else {
                // Extract from @var.
                if (count($matches) < 3) {
                    $description = '';
                } else {
                    $description = trim($matches[2]);
                }
            }

            // Establish type.
            if (!empty($wstype)) {
                // Use @wstype.
                $type = $wstype;
            } else {
                // Extract from @var.
                $aliases = [
                    'bool' => PARAM_BOOL,
                    'boolean' => PARAM_BOOL,
                    'str' => PARAM_RAW,
                    'string' => PARAM_RAW,
                    'int' => PARAM_INT,
                    'integer' => PARAM_INT,
                    'float' => PARAM_FLOAT,
                    'moodle_url' => PARAM_URL,
                ];
                $type = trim($matches[1]);
                if (isset($aliases[$type])) {
                    $type = $aliases[$type];
                } else if (isset($aliases[str_replace('\\', '', $type)])) {
                    $type = $aliases[str_replace('\\', '', $type)];
                } else {
                    if ($type === 'stdClass') {
                        throw new coding_exception(
                            'Property ' . $name . ' using unspoported stdClass in class ' . $this->classname
                        );
                    }
                    $isarray = strpos($type, '[]');
                    $classdetected = $this->get_class_from_type($type);

                    if (empty($classdetected)) {
                        throw new coding_exception(
                            $this->param_error('Unknown / incompatible var type ' . $type, $name)
                        );
                    }

                    if ($classdetected === $this->classname) {
                        throw new coding_exception($this->param_error('Class definition infinite recursion ', $name));
                    }
                }
            }

            if ($classdetected) {
                $defineobj = new definition_helper($classdetected);
                $structure = new external_single_structure($defineobj->get_definition(), $description, $wsreq);
                if ($isarray) {
                    $this->definition[$name] = new external_multiple_structure($structure, $description, $wsreq);
                } else {
                    $this->definition[$name] = $structure;
                }
            } else {
                $this->definition[$name] = new external_value($type, $description, $wsreq, null, $wsallownull);
            }
        }
    }

    /**
     * Convenience function.
     * @param string|object $classorobject
     */
    public static function define_class_for_webservice($classorobject) {
        $helper = new definition_helper($classorobject);
        return $helper->get_definition();
    }
}
