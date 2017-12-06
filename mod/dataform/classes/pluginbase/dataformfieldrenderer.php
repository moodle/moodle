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
 * @package dataformfield
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

defined('MOODLE_INTERNAL') or die;

/**
 * Base class for field patterns
 */
abstract class dataformfieldrenderer {

    const PATTERN_SHOW_IN_MENU = 0;
    const PATTERN_CATEGORY = 1;

    const PATTERN_REQUIRED = '*';
    const PATTERN_NOEDIT = '!';

    protected $_field = null;

    /**
     * Constructor
     */
    public function __construct(&$field) {
        $this->_field = $field;
    }

    /**
     * Search and collate field patterns that occur in given text.
     *
     * @param string Text that may contain field patterns
     * @param array Optional list of patterns to search in text
     * @return array Field patterns found in the text
     */
    public function search($text, array $patterns = null) {
        $field = $this->_field;
        $fieldid = $field->id;
        $fieldname = $this->_field->name;

        $found = array();

        // Capture label patterns.
        if (strpos($text, "[[T@$fieldname]]") !== false and $field->label) {
            $found["[[T@$fieldname]]"] = "[[T@$fieldname]]";

            $text = str_replace("[[T@$fieldname]]", $field->label, $text);
        }
        // Search and collate field patterns.
        $patterns = $patterns ? $patterns : array_keys($this->patterns());

        foreach ($patterns as $pattern) {
            if (strpos($text, $pattern) !== false) {
                $found[$pattern] = $pattern;
            }
            // With required rule *.
            $patternrequired = '[['. self::PATTERN_REQUIRED. trim($pattern, '[');
            if (strpos($text, $patternrequired) !== false) {
                $found[$patternrequired] = $patternrequired;
            }
            // With noedit rule !.
            $patternnoedit = '[['. self::PATTERN_NOEDIT. trim($pattern, '[');
            if (strpos($text, $patternnoedit) !== false) {
                $found[$patternnoedit] = $patternnoedit;
            }
        }
        return array_values($found);
    }

    /**
     * Cleans a pattern from auxiliary indicators (e.g. * for required)
     */
    protected function add_clean_pattern_keys(array $patterns) {
        $keypatterns = array();
        $patternrules = array('[['. self::PATTERN_REQUIRED, '[['. self::PATTERN_NOEDIT);
        foreach ($patterns as $pattern) {
            $keypatterns[$pattern] = str_replace($patternrules, '[[', $pattern);
        }
        return $keypatterns;
    }

    /**
     * Returns true if the pattern contains the required flag (*).
     *
     * @return bool
     */
    public function is_required($pattern) {
        return (strpos(trim($pattern, '['), self::PATTERN_REQUIRED) === 0);
    }

    /**
     * Returns true if the pattern contains the no-edit flag (!).
     *
     * @return bool
     */
    public function is_noedit($pattern) {
        return (strpos(trim($pattern, '['), self::PATTERN_NOEDIT) === 0);
    }

    /**
     *
     * @return array
     */
    public function pluginfile_patterns() {
        return array();
    }

    /**
     * Returns import settings for the specified field pattern
     * that consist of a list of mform elements to group, and
     * a list of corresponding labels.
     * By default adds the specified pattern name. Subclasses can override
     * to exclude patterns from import or deny import at all.
     *
     * @param moodleform $mform
     * @param string $patternname
     * @param string $header The default value of the name element
     * @return array
     */
    public function get_pattern_import_settings(&$mform, $patternname, $header) {
        $field = $this->_field;
        $fieldid = $field->id;
        $fieldname = $field->name;

        $patternpart = trim(str_replace($fieldname, '', $patternname), ':');
        $name = "f_{$fieldid}_$patternpart";

        $grp = array();
        $grp[] = &$mform->createElement('text', "{$name}_name", null, array('size' => '16'));

        $mform->setType("{$name}_name", PARAM_TEXT);
        $mform->setDefault("{$name}_name", $header);

        return array($grp, array());
    }

    /**
     * Generates and returns the field patterns menu by category from
     * the field's patterns list.
     *
     * @param bool $showall Whether to show all patterns or only those marked for showing.
     * @return array Associative array of associative arrays
     */
    public final function get_menu($showall = false) {
        // The default menu category for fields.
        $patternsmenu = array();
        foreach ($this->patterns() as $tag => $pattern) {
            if ($showall or $pattern[self::PATTERN_SHOW_IN_MENU]) {
                // Which category.
                if (!empty($pattern[self::PATTERN_CATEGORY])) {
                    $cat = $pattern[self::PATTERN_CATEGORY];
                } else {
                    $cat = get_string('fields', 'dataform');
                }
                // Prepare array.
                if (!isset($patternsmenu[$cat])) {
                    $patternsmenu[$cat] = array();
                }
                // Add tag.
                $patternsmenu[$cat][$tag] = $tag;
            }
        }
        return $patternsmenu;
    }

    /**
     * Returns the list of replacements for the specified field patterns.
     *
     * @param array $patterns
     * @param stdClass $entry
     * @param array $options
     * @return array pattern => replacement
     */
    public function get_replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;

        // Are we editing this field?
        $editing = (!empty($options['edit']) and $field->is_editable($entry));
        $options['edit'] = $editing;

        // If we have a template put it aside for later.
        if ($fieldtemplate = array_search("[[T@$fieldname]]", $patterns) or $fieldtemplate !== false) {
            unset($patterns[$fieldtemplate]);
        }

        // Get the field replacements.
        $patterns = $this->add_clean_pattern_keys($patterns);
        $replacements = $this->replacements($patterns, $entry, $options);

        // No field template.
        if ($fieldtemplate === false) {
            return $replacements;
        }

        // Empty field template.
        if (!$field->label) {
            $replacements["[[T@$fieldname]]"] = null;
            return $replacements;
        }

        // Field template with content.
        if ($editing) {
            // Editing.
            $replacements["[[T@$fieldname]]"] = array(array($this , 'parse_template'), array($replacements));
            return $replacements;
        } else {
            // Browsing.
            $template = $field->label;
            foreach ($replacements as $pattern => $replacement) {
                // Skip the template pattern.
                if ($pattern == "[[T@$fieldname]]") {
                    continue;
                }

                $template = str_replace($pattern, $replacement, $template);
            }
            $replacements["[[T@$fieldname]]"] = $template;
        }
        return $replacements;
    }

    /**
     *
     */
    public function get_patterns() {
        return array_keys($this->patterns());
    }

    /**
     *
     */
    public function get_view_patterns() {
        return $this->view_patterns();
    }

    /**
     * @param array $patterns array of arrays of pattern replacement pairs
     */
    public function parse_template(&$mform, $definitions) {
        $field = $this->_field;
        $patterns = array_keys($definitions);

        $delims = implode('|', $patterns);

        // Escape [ and ] and the pattern rule character *.
        $delims = quotemeta($delims);

        $parts = preg_split("/($delims)/", $field->label, null, PREG_SPLIT_DELIM_CAPTURE);
        $htmlparts = '';
        foreach ($parts as $part) {
            if (in_array($part, $patterns)) {
                if ($def = $definitions[$part]) {
                    if (is_array($def)) {
                        if ($htmlparts) {
                            $mform->addElement('html', $htmlparts);
                            $htmlparts = '';
                        }
                        list($func, $params) = $def;
                        call_user_func_array($func, array_merge(array($mform), $params));
                    }
                } else {
                    $htmlparts .= $def;
                }
            } else {
                $htmlparts .= $part;
            }
        }
        if ($htmlparts) {
            $mform->addElement('html', $htmlparts);
        }
    }

    /**
     *
     */
    public function validate_data($entryid, $tags, $data) {
        return null;
    }

    /**
     * Returns list of replacements for the specified field patterns.
     *
     * @param array $patterns
     * @param stdClass $entry
     * @param array $options
     * @return array pattern => replacement
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        return array_fill_keys($patterns, '');
    }

    /**
     * Array of patterns this field supports
     * The label pattern should always be first where applicable
     * so that it is processed first in view templates
     * so that in turn patterns it may contain could be processed.
     *
     * @return array pattern => array(visible in menu, category)
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = array();
        $patterns["[[T@$fieldname]]"] = array(true, $fieldname);

        return $patterns;
    }

    /**
     * Array of patterns this field supports in the view template
     * (that is, outside an entry). These patterns will be listed
     * in the view patterns selector in the view configuration form.
     * These patterns must start with fieldname: and then a specific tag.
     *
     * @return array pattern => array(visible in menu, category)
     */
    protected function view_patterns() {
        return array();
    }
}
