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
 * Autocomplete as you type form element.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_admin\setting\setting;

class configselect_autocomplete extends \admin_setting_configselect {
    /** @var boolean $tags Should we allow typing new entries to the field? */
    protected $tags = false;
    /** @var string $ajax Name of an AMD module to send/process ajax requests. */
    protected $ajax = '';
    /** @var string $placeholder Placeholder text for an empty list. */
    protected $placeholder = '';
    /** @var bool $casesensitive Whether the search has to be case-sensitive. */
    protected $casesensitive = false;
    /** @var bool $showsuggestions Show suggestions by default - but this can be turned off. */
    protected $showsuggestions = true;
    /** @var string $noselectionstring String that is shown when there are no selections. */
    protected $noselectionstring = '';

    /**
     * Returns XHTML select field and wrapping div(s)
     *
     * @param string $data the option to show as selected
     * @param string $query
     * @return string XHTML field and wrapping div
     */
    public function output_html($data, $query='') {
        global $PAGE;

        $html = parent::output_html($data, $query);

        if ($html === '') {
            return $html;
        }

        $this->placeholder = get_string('search');

        $params = array('#' . $this->get_id(), $this->tags, $this->ajax,
            $this->placeholder, $this->casesensitive, $this->showsuggestions, $this->noselectionstring);

        // Load autocomplete wrapper for select2 library.
        $PAGE->requires->js_call_amd('core/form-autocomplete', 'enhance', $params);

        return $html;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configselect_autocomplete::class, \admin_setting_configselect_autocomplete::class);
