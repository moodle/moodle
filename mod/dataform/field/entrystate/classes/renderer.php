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
 * @subpackage entrystate
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die();

/**
 *
 */
class dataformfield_entrystate_renderer extends mod_dataform\pluginbase\dataformfieldrenderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;

        $edit = !empty($options['edit']);
        $replacements = array();
        $statenames = $field->states;

        foreach ($patterns as $pattern) {
            if ($pattern == "[[$fieldname]]") {
                if (!$entry or $entry->id < 0 or  $edit) {
                    $replacements[$pattern] = array(array($this, 'display_edit'), array($entry));
                } else {
                    $replacements[$pattern] = $this->display_browse($entry);
                }
                continue;
            }

            list(, $patternstate, $qualifier) = array_pad(explode(':', trim($pattern, '[]'), 3), 3, null);
            if ($patternstate == 'current' and $qualifier != null) {
                if ($qualifier == 'key') {
                    $replacements[$pattern] = $entry->state;
                } else if ($qualifier == 'name' and array_key_exists($entry->state, $statenames)) {
                    $replacements[$pattern] = $statenames[$entry->state];
                }
            } else if ($patternstate == 'current' or $patternstate == 'state') {
                // Current state.
                $currentstate = empty($entry->state) ? 0 : $entry->state;
                $replacements[$pattern] = $this->get_state_display($entry, $currentstate);
            } else if ($statekey = array_search($patternstate, $statenames) or $statekey !== false) {
                // Existing state.
                $replacements[$pattern] = $this->get_state_display($entry, $statekey);
            } else if ($patternstate == 'bulkinstate') {
                $replacements[$pattern] = '';
            }
        }

        return $replacements;
    }

    /**
     *
     */
    public function display_edit(&$mform, $entry, array $options = null) {

        $field = $this->_field;
        $fieldid = $field->id;
        $entryid = $entry->id;
        $fieldname = "field_{$fieldid}_{$entryid}";

        $currentstate = !empty($entry->state) ? $entry->state : 0;
        if ($states = $field->get_user_transition_states($entry)) {
            $mform->addElement('select', $fieldname, null, $states);
            $mform->setDefault($fieldname, $currentstate);
        } else {
            $mform->addElement('html', $field->states[$currentstate]);
        }
    }

    /**
     *
     */
    public function display_browse($entry, $options = null) {
        global $PAGE;

        if ($html = $this->get_browse_content($entry)) {
            $field = $this->_field;
            // Initialize AJAX.
            $config = array(
                'd' => $field->df->id,
                'fieldid' => $field->id,
                'entryid' => $entry->id,
                'sesskey' => sesskey()
            );
            $this->initialise_javascript($PAGE, array($config));

            $elemid = "entrystates_{$entry->id}_$field->id";
            return html_writer::tag('div', $html, array('id' => $elemid, 'class' => 'entrystates-wrapper'));
        }

        return null;
    }

    /**
     *
     */
    public function get_browse_content($entry) {
        $field = $this->_field;

        $statedisplay = array();

        if ($states = $field->states) {
            foreach ($states as $key => $state) {
                if ($display = $this->get_state_display($entry, $key)) {
                    $statedisplay[] = $display;
                }
            }
        }
        return implode(' &rarr; ', $statedisplay);
    }

    /**
     *
     */
    public function get_state_display($entry, $statekey) {
        $field = $this->_field;
        $states = $field->states;
        if (!empty($states[$statekey])) {
            $statename = $states[$statekey];
            $stateclassname = str_replace(' ', '_', $statename);
            if ($statekey == $entry->state) {
                return html_writer::tag('span', $statename, array('class' => "entrystate $stateclassname currentstate"));
            } else {
                $statetext = html_writer::tag('span', $statename, array('class' => "entrystate $stateclassname"));
                if ($field->can_instate($entry, $statekey)) {
                    $params = array('entryid' => $entry->id, 'fieldid' => $field->id, 'state' => $statekey, 'sesskey' => sesskey());
                    $url = new moodle_url($entry->baseurl, $params);
                    $link = html_writer::link($url, $statetext, array('id' => "entrystate_{$entry->id}_$statekey"));
                    return $link;
                } else {
                    return $statetext;
                }
            }
        }
        return null;
    }

    /**
     * Overriding {@link dataformfieldrenderer::get_pattern_import_settings()}
     * to allow only the base pattern.
     */
    public function get_pattern_import_settings(&$mform, $patternname, $header) {
        // Only [[fieldname]] can be imported.
        if ($patternname != $this->_field->name) {
            return array(array(), array());
        }

        return parent::get_pattern_import_settings($mform, $patternname, $header);
    }

    /**
     * Initialises JavaScript to enable AJAX dis/approval on the provided page.
     *
     * @param moodle_page $page
     * @return true always returns true
     */
    protected function initialise_javascript(moodle_page $page, $config = null) {
        $page->requires->yui_module(
            'moodle-dataformfield_entrystate-stater',
            'M.dataformfield_entrystate.stater.init',
            $config
        );

        return true;
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $field = $this->_field;
        $fieldname = $field->name;
        $cat = $fieldname;

        $patterns = array();
        $patterns["[[$fieldname]]"] = array(true, $cat);
        $patterns["[[$fieldname:current]]"] = array(true, $cat);
        $patterns["[[$fieldname:current:key]]"] = array(false);
        $patterns["[[$fieldname:current:name]]"] = array(false);
        $patterns["[[$fieldname:state]]"] = array(false);
        if ($states = $field->states) {
            foreach ($states as $state) {
                $patterns["[[$fieldname:$state]]"] = array(false);
            }
        }

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
        $fieldname = $this->_field->name;

        $patterns = array();
        $patterns["[[$fieldname:bulkinstate]]"] = array(true, $fieldname);

        return $patterns;
    }

}
