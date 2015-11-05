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
 * Tag autocomplete field.
 *
 * Contains HTML class for editing tags, both official and personal.
 *
 * @package   core_form
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->libdir . '/form/autocomplete.php');

/**
 * Form field type for editing tags.
 *
 * HTML class for editing tags, both official and personal.
 *
 * @package   core_form
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_tags extends MoodleQuickForm_autocomplete {
    /**
     * Inidcates that the user should be the usual interface, with the official
     * tags listed seprately, and a text box where they can type anything.
     * @var int
     */
    const DEFAULTUI = 'defaultui';

    /**
     * Indicates that the user should only be allowed to select official tags.
     * @var int
     */
    const ONLYOFFICIAL = 'onlyofficial';

    /**
     * Indicates that the user should just be given a text box to type in (they
     * can still type official tags though.
     * @var int
     */
    const NOOFFICIAL = 'noofficial';

    /**
     * @var boolean $showingofficial Official tags shown? (if not, then don't show link to manage official tags).
     */
    protected $showingofficial = false;

    /**
     * Constructor
     *
     * @param string $elementName Element name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    public function __construct($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        if (!isset($options['display'])) {
            $options['display'] = self::DEFAULTUI;
        }

        $this->showingofficial = $options['display'] != MoodleQuickForm_tags::NOOFFICIAL;

        $validoptions = array();
        if ($this->showingofficial) {
            $validoptions = $this->load_official_tags();
        }
        // 'tags' option allows us to type new tags.
        if ($options['display'] == MoodleQuickForm_tags::ONLYOFFICIAL) {
            $attributes['tags'] = false;
        } else {
            $attributes['tags'] = true;
        }
        $attributes['multiple'] = 'multiple';
        $attributes['placeholder'] = get_string('entertags', 'tag');
        $attributes['showsuggestions'] = $this->showingofficial;

        parent::__construct($elementName, $elementLabel, $validoptions, $attributes);
    }

    /**
     * Old syntax of class constructor for backward compatibility.
     */
    public function MoodleQuickForm_tags($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        self::__construct($elementName, $elementLabel, $options, $attributes);
    }

    /**
     * Returns HTML for select form element.
     *
     * @return string
     */
    function toHtml(){
        global $CFG, $OUTPUT;

        if (empty($CFG->usetags)) {
            debugging('A tags formslib field has been created even thought $CFG->usetags is false.', DEBUG_DEVELOPER);
        }

        $managelink = '';
        if (has_capability('moodle/tag:manage', context_system::instance()) && $this->showingofficial) {
            $url = $CFG->wwwroot .'/tag/manage.php';
            $managelink = ' ' . $OUTPUT->action_link($url, get_string('manageofficialtags', 'tag'));
        }

        return parent::toHTML() . $managelink;
    }

    /**
     * Internal function to load official tags
     *
     * @access protected
     */
    protected function load_official_tags() {
        global $CFG, $DB;

        $namefield = empty($CFG->keeptagnamecase) ? 'name' : 'rawname';
        $records = $DB->get_records('tag', array('tagtype' => 'official'), $namefield, 'id,' . $namefield);
        $tags = array();

        foreach ($records as $record) {
            $tags[$record->$namefield] = $record->$namefield;
        }
        return $tags;
    }

}
