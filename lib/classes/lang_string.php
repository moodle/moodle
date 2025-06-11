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

use core\exception\coding_exception;
use stdClass;

/**
 * The lang_string class
 *
 * This special class is used to create an object representation of a string request.
 * It is special because processing doesn't occur until the object is first used.
 * The class was created especially to aid performance in areas where strings were
 * required to be generated but were not necessarily used.
 * As an example the admin tree when generated uses over 1500 strings, of which
 * normally only 1/3 are ever actually printed at any time.
 * The performance advantage is achieved by not actually processing strings that
 * aren't being used, as such reducing the processing required for the page.
 *
 * How to use the lang_string class?
 *     There are two methods of using the lang_string class, first through the
 *     forth argument of the get_string function, and secondly directly.
 *     The following are examples of both.
 * 1. Through get_string calls e.g.
 *     $string = get_string($identifier, $component, $a, true);
 *     $string = get_string('yes', 'moodle', null, true);
 * 2. Direct instantiation
 *     $string = new lang_string($identifier, $component, $a, $lang);
 *     $string = new lang_string('yes');
 *
 * How do I use a lang_string object?
 *     The lang_string object makes use of a magic __toString method so that you
 *     are able to use the object exactly as you would use a string in most cases.
 *     This means you are able to collect it into a variable and then directly
 *     echo it, or concatenate it into another string, or similar.
 *     The other thing you can do is manually get the string by calling the
 *     lang_strings out method e.g.
 *         $string = new lang_string('yes');
 *         $string->out();
 *     Also worth noting is that the out method can take one argument, $lang which
 *     allows the developer to change the language on the fly.
 *
 * When should I use a lang_string object?
 *     The lang_string object is designed to be used in any situation where a
 *     string may not be needed, but needs to be generated.
 *     The admin tree is a good example of where lang_string objects should be
 *     used.
 *     A more practical example would be any class that requires strings that may
 *     not be printed (after all classes get rendered by renderers and who knows
 *     what they will do ;))
 *
 * When should I not use a lang_string object?
 *     Don't use lang_strings when you are going to use a string immediately.
 *     There is no need as it will be processed immediately and there will be no
 *     advantage, and in fact perhaps a negative hit as a class has to be
 *     instantiated for a lang_string object, however get_string won't require
 *     that.
 *
 * Limitations:
 * 1. You cannot use a lang_string object as an array offset. Doing so will
 *     result in PHP throwing an error. (You can use it as an object property!)
 *
 * @package     core
 * @copyright   2011 Sam Hemelryk
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lang_string {

    /** @var string The strings component. Default '' */
    protected ?string $component = '';

    /** @var array|stdClass Any arguments required for the string. Default null */
    protected mixed $a = null;

    /** @var string The processed string (once processed) */
    protected ?string $string = null;

    /**
     * A special boolean. If set to true then the object has been woken up and
     * cannot be regenerated. If this is set then $this->string MUST be used.
     * @var bool
     */
    protected bool $forcedstring = false;

    /**
     * Constructs a lang_string object
     *
     * This function should do as little processing as possible to ensure the best
     * performance for strings that won't be used.
     *
     * @param string $identifier The strings identifier
     * @param string|null $component The strings component
     * @param mixed $a Any arguments the string requires
     * @param string|null $lang The language to use when processing the string.
     * @throws coding_exception
     */
    public function __construct(
        /** @var string The strings identifier */
        protected readonly string $identifier,
        ?string $component = '',
        mixed $a = null,
        /** @var string The language to use when processing the string*/
        protected readonly ?string $lang = null,
    ) {
        if (empty($component)) {
            $component = 'moodle';
        }

        $this->component = $component;

        // We MUST duplicate $a to ensure that it if it changes by reference those
        // changes are not carried across.
        // To do this we always ensure $a or its properties/values are strings
        // and that any properties/values that arn't convertable are forgotten.
        if ($a !== null) {
            if (is_scalar($a)) {
                $this->a = $a;
            } else if ($a instanceof lang_string) {
                $this->a = $a->out();
            } else if (is_object($a) || is_array($a)) {
                $a = (array)$a;
                $this->a = [];
                foreach ($a as $key => $value) {
                    // Make sure conversion errors don't get displayed (results in '').
                    if (is_array($value)) {
                        $this->a[$key] = '';
                    } else if (is_object($value)) {
                        if (method_exists($value, '__toString')) {
                            $this->a[$key] = $value->__toString();
                        } else {
                            $this->a[$key] = '';
                        }
                    } else {
                        $this->a[$key] = (string)$value;
                    }
                }
            }
        }

        if (debugging(false, DEBUG_DEVELOPER)) {
            if (clean_param($this->identifier, PARAM_STRINGID) == '') {
                throw new coding_exception('Invalid string identifier. Most probably some illegal character is part of ' .
                    'the string identifier. Please check your string definition');
            }
            if (!empty($this->component) && clean_param($this->component, PARAM_COMPONENT) == '') {
                throw new coding_exception('Invalid string compontent. Please check your string definition');
            }
            if (!get_string_manager()->string_exists($this->identifier, $this->component)) {
                debugging('String does not exist. Please check your string definition for '.$this->identifier.'/'.$this->component,
                    DEBUG_DEVELOPER);
            }
        }
    }

    /**
     * Processes the string.
     *
     * This function actually processes the string, stores it in the string property
     * and then returns it.
     * You will notice that this function is VERY similar to the get_string method.
     * That is because it is pretty much doing the same thing.
     * However as this function is an upgrade it isn't as tolerant to backwards
     * compatibility.
     *
     * @return string
     * @throws coding_exception
     */
    protected function get_string(): string {
        global $CFG;

        // Check if we need to process the string.
        if ($this->string === null) {
            // Check the quality of the identifier.
            if ($CFG->debugdeveloper && clean_param($this->identifier, PARAM_STRINGID) === '') {
                throw new coding_exception('Invalid string identifier. Most probably some illegal character is part of ' .
                    'the string identifier. Please check your string definition', DEBUG_DEVELOPER);
            }

            // Process the string.
            $this->string = get_string_manager()->get_string($this->identifier, $this->component, $this->a, $this->lang);
            // Debugging feature lets you display string identifier and component.
            if (isset($CFG->debugstringids) && $CFG->debugstringids && optional_param('strings', 0, PARAM_INT)) {
                $this->string .= ' {' . $this->identifier . '/' . $this->component . '}';
            }
        }
        // Return the string.
        return $this->string;
    }

    /**
     * Returns the string
     *
     * @param string $lang The langauge to use when processing the string
     * @return string
     */
    public function out($lang = null): string {
        if ($lang !== null && $lang != $this->lang && ($this->lang == null && $lang != current_language())) {
            if ($this->forcedstring) {
                debugging('lang_string objects that have been used cannot be printed in another language. ('.$this->lang.' used)',
                    DEBUG_DEVELOPER);
                return $this->get_string();
            }
            $translatedstring = new lang_string($this->identifier, $this->component, $this->a, $lang);
            return $translatedstring->out();
        }
        return $this->get_string();
    }

    /**
     * Magic __toString method for printing a string
     *
     * @return string
     */
    public function __toString() {
        return $this->get_string();
    }

    /**
     * Magic __set_state method used for var_export
     *
     * @param array $array
     * @return self
     */
    public static function __set_state(array $array): self {
        $tmp = new lang_string($array['identifier'], $array['component'], $array['a'], $array['lang']);
        $tmp->string = $array['string'];
        $tmp->forcedstring = $array['forcedstring'];
        return $tmp;
    }

    /**
     * Prepares the lang_string for sleep and stores only the forcedstring and
     * string properties... the string cannot be regenerated so we need to ensure
     * it is generated for this.
     *
     * @return array
     */
    public function __sleep() {
        $this->get_string();
        $this->forcedstring = true;
        return ['forcedstring', 'string', 'lang'];
    }

    /**
     * Returns the identifier.
     *
     * @return string
     */
    public function get_identifier(): string {
        return $this->identifier;
    }

    /**
     * Returns the component.
     *
     * @return string
     */
    public function get_component(): string {
        return $this->component;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(lang_string::class, \lang_string::class);
