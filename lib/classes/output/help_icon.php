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

namespace core\output;

use moodle_url;
use stdClass;

/**
 * Data structure representing a help icon.
 *
 * @copyright 2010 Petr Skoda (info@skodak.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class help_icon implements renderable, templatable {
    /**
     * @var string lang pack identifier (without the "_help" suffix),
     * both get_string($identifier, $component) and get_string($identifier.'_help', $component)
     * must exist.
     */
    public $identifier;

    /**
     * @var string Component name, the same as in get_string()
     */
    public $component;

    /**
     * @var string Extra descriptive text next to the icon
     */
    public $linktext = null;

    /**
     * @var mixed An object, string or number that can be used within translation strings
     */
    public $a = null;

    /**
     * Constructor
     *
     * @param string $identifier string for help page title,
     *  string with _help suffix is used for the actual help text.
     *  string with _link suffix is used to create a link to further info (if it exists)
     * @param string $component
     * @param string|object|array|int $a An object, string or number that can be used
     *      within translation strings
     */
    public function __construct($identifier, $component, $a = null) {
        $this->identifier = $identifier;
        $this->component  = $component;
        $this->a = $a;
    }

    /**
     * Verifies that both help strings exists, shows debug warnings if not
     */
    public function diag_strings() {
        $sm = get_string_manager();
        if (!$sm->string_exists($this->identifier, $this->component)) {
            debugging("Help title string does not exist: [$this->identifier, $this->component]");
        }
        if (!$sm->string_exists($this->identifier . '_help', $this->component)) {
            debugging("Help contents string does not exist: [{$this->identifier}_help, $this->component]");
        }
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $title = get_string($this->identifier, $this->component, $this->a);

        if (empty($this->linktext)) {
            $alt = get_string('helpprefix2', '', trim($title, ". \t"));
        } else {
            $alt = get_string('helpwiththis');
        }

        $data = get_formatted_help_string($this->identifier, $this->component, false, $this->a);

        $data->alt = $alt;
        $data->icon = (new pix_icon('help', $alt, 'core'))->export_for_template($output);
        $data->linktext = $this->linktext;
        $data->title = get_string('helpprefix2', '', trim($title, ". \t"));

        $options = [
            'component' => $this->component,
            'identifier' => $this->identifier,
            'lang' => current_language(),
        ];

        // Debugging feature lets you display string identifier and component.
        if (isset($CFG->debugstringids) && $CFG->debugstringids && optional_param('strings', 0, PARAM_INT)) {
            $options['strings'] = 1;
        }

        $data->url = (new moodle_url('/help.php', $options))->out(false);
        $data->ltr = !right_to_left();
        return $data;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(help_icon::class, \help_icon::class);
