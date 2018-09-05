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
 * Adds export_for_template behaviour to an mform element in a consistent and predictable way.
 *
 * @package   core_form
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

// Some form elements are used before $CFG is created - do not rely on it here.
require_once(__DIR__ . '/../outputcomponents.php');

/**
 * templatable_form_element trait.
 *
 * @package   core_form
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait templatable_form_element {

    /**
     * Function to export the renderer data in a format that is suitable for a
     * mustache template. This means:
     * 1. No complex types - only stdClass, array, int, string, float, bool
     * 2. Any additional info that is required for the template is pre-calculated (e.g. capability checks).
     *
     * This trait can be used as-is for simple form elements - or imported with a different name
     * so it can be extended with additional context variables before being returned.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        $context = [];

        // Not all elements have all of these attributes - but they are common enough to be valid for a few.
        $standardattributes = ['id', 'name', 'label', 'multiple', 'checked', 'error', 'size', 'value', 'type'];
        $standardproperties = ['helpbutton', 'hiddenLabel'];

        // Standard attributes.
        foreach ($standardattributes as $attrname) {
            $value = $this->getAttribute($attrname);
            $context[$attrname] = $value;
        }

        // Standard class properties.
        foreach ($standardproperties as $propname) {
            $classpropname = '_' . $propname;
            $context[strtolower($propname)] = isset($this->$classpropname) ? $this->$classpropname : false;
        }
        $extraclasses = $this->getAttribute('class');

        // Special wierd named property.
        $context['frozen'] = !empty($this->_flagFrozen);
        $context['hardfrozen'] = !empty($this->_flagFrozen) && empty($this->_persistantFreeze);

        // Other attributes.
        $otherattributes = [];
        foreach ($this->getAttributes() as $attr => $value) {
            if (!in_array($attr, $standardattributes) && $attr != 'class' && !is_object($value)) {
                $otherattributes[] = $attr . '="' . s($value) . '"';
            }
        }
        $context['extraclasses'] = $extraclasses;
        $context['type'] = $this->getType();
        $context['attributes'] = implode(' ', $otherattributes);
        $context['emptylabel'] = ($this->getLabel() === '');

        // Elements with multiple values need array syntax.
        if ($this->getAttribute('multiple')) {
            $context['name'] = $context['name'] . '[]';
        }

        return $context;
    }
}
