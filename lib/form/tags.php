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
 * Contains HTML class for editing tags, both standard and not.
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
 * HTML class for editing tags, both standard and not.
 *
 * @package   core_form
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_tags extends MoodleQuickForm_autocomplete {
    /**
     * Inidcates that the user should be the usual interface, with the official
     * tags listed seprately, and a text box where they can type anything.
     * @deprecated since 3.1
     * @var int
     */
    const DEFAULTUI = 'defaultui';

    /**
     * Indicates that the user should only be allowed to select official tags.
     * @deprecated since 3.1
     * @var int
     */
    const ONLYOFFICIAL = 'onlyofficial';

    /**
     * Indicates that the user should just be given a text box to type in (they
     * can still type official tags though.
     * @deprecated since 3.1
     * @var int
     */
    const NOOFFICIAL = 'noofficial';

    /**
     * @var boolean $showstandard Standard tags suggested? (if not, then don't show link to manage standard tags).
     */
    protected $showstandard = false;

    /**
     * Options passed when creating an element.
     * @var array
     */
    protected $tagsoptions = array();

    /**
     * Constructor
     *
     * @param string $elementName Element name
     * @param mixed $elementLabel Label(s) for an element
     * @param array $options Options to control the element's display
     * @param mixed $attributes Either a typical HTML attribute string or an associative array.
     */
    public function __construct($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        $validoptions = array();

        if (!empty($options)) {
            // Only execute it when the element was created and $options has values set by user.
            // In onQuickFormEvent() we make sure that $options is not empty even if developer left it empty.
            $showstandard = core_tag_tag::BOTH_STANDARD_AND_NOT;
            if (isset($options['showstandard'])) {
                $showstandard = $options['showstandard'];
            } else if (isset($options['display'])) {
                debugging('Option "display" is deprecated, each tag area can be configured to show standard tags or not ' .
                    'by admin or manager. If it is necessary for the developer to override it, please use "showstandard" option',
                    DEBUG_DEVELOPER);
                if ($options['display'] === self::NOOFFICIAL) {
                    $showstandard = core_tag_tag::HIDE_STANDARD;
                } else if ($options['display'] === self::ONLYOFFICIAL) {
                    $showstandard = core_tag_tag::STANDARD_ONLY;
                }
            } else if (!empty($options['component']) && !empty($options['itemtype'])) {
                $showstandard = core_tag_area::get_showstandard($options['component'], $options['itemtype']);
            }

            $this->tagsoptions = $options;

            $this->showstandard = ($showstandard != core_tag_tag::HIDE_STANDARD);
            if ($this->showstandard) {
                $validoptions = $this->load_standard_tags();
            }
            // Option 'tags' allows us to type new tags.
            $attributes['tags'] = ($showstandard != core_tag_tag::STANDARD_ONLY);
            $attributes['multiple'] = 'multiple';
            $attributes['placeholder'] = get_string('entertags', 'tag');
            $attributes['showsuggestions'] = $this->showstandard;
        }

        parent::__construct($elementName, $elementLabel, $validoptions, $attributes);
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    public function onQuickFormEvent($event, $arg, &$caller) {
        if ($event === 'createElement') {
            if (!is_array($arg[2])) {
                $arg[2] = [];
            }
            $arg[2] += array('itemtype' => '', 'component' => '');
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * Checks if tagging is enabled for this itemtype
     *
     * @return boolean
     */
    protected function is_tagging_enabled() {
        if (!empty($this->tagsoptions['itemtype']) && !empty($this->tagsoptions['component'])) {
            $enabled = core_tag_tag::is_enabled($this->tagsoptions['component'], $this->tagsoptions['itemtype']);
            if ($enabled === false) {
                return false;
            }
        }
        // Backward compatibility with code developed before Moodle 3.0 where itemtype/component were not specified.
        return true;
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_tags($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $options, $attributes);
    }

    /**
     * Finds the tag collection to use for standard tag selector
     *
     * @return int
     */
    protected function get_tag_collection() {
        if (empty($this->tagsoptions['tagcollid']) && (empty($this->tagsoptions['itemtype']) ||
                empty($this->tagsoptions['component']))) {
            debugging('You need to specify \'itemtype\' and \'component\' of the tagged '
                    . 'area in the tags form element options',
                    DEBUG_DEVELOPER);
        }
        if (!empty($this->tagsoptions['tagcollid'])) {
            return $this->tagsoptions['tagcollid'];
        }
        if ($this->tagsoptions['itemtype']) {
            $this->tagsoptions['tagcollid'] = core_tag_area::get_collection($this->tagsoptions['component'],
                    $this->tagsoptions['itemtype']);
        } else {
            $this->tagsoptions['tagcollid'] = core_tag_collection::get_default();
        }
        return $this->tagsoptions['tagcollid'];
    }

    /**
     * Returns HTML for select form element.
     *
     * @return string
     */
    function toHtml(){
        global $OUTPUT;

        $managelink = '';
        if (has_capability('moodle/tag:manage', context_system::instance()) && $this->showstandard) {
            $url = new moodle_url('/tag/manage.php', array('tc' => $this->get_tag_collection()));
            $managelink = ' ' . $OUTPUT->action_link($url, get_string('managestandardtags', 'tag'));
        }

        return parent::toHTML() . $managelink;
    }

    /**
     * Accepts a renderer
     *
     * @param HTML_QuickForm_Renderer $renderer An HTML_QuickForm_Renderer object
     * @param bool $required Whether a group is required
     * @param string $error An error message associated with a group
     */
    public function accept(&$renderer, $required = false, $error = null) {
        if ($this->is_tagging_enabled()) {
            $renderer->renderElement($this, $required, $error);
        } else {
            $renderer->renderHidden($this);
        }
    }

    /**
     * Internal function to load standard tags
     */
    protected function load_standard_tags() {
        global $CFG, $DB;
        if (!$this->is_tagging_enabled()) {
            return array();
        }
        $namefield = empty($CFG->keeptagnamecase) ? 'name' : 'rawname';
        $tags = $DB->get_records_menu('tag',
            array('isstandard' => 1, 'tagcollid' => $this->get_tag_collection()),
            $namefield, 'id,' . $namefield);
        return array_combine($tags, $tags);
    }

    /**
     * Returns a 'safe' element's value
     *
     * @param  array  $submitValues array of submitted values to search
     * @param  bool   $assoc        whether to return the value as associative array
     * @return mixed
     */
    public function exportValue(&$submitValues, $assoc = false) {
        if (!$this->is_tagging_enabled()) {
            return $assoc ? array($this->getName() => array()) : array();
        }

        return parent::exportValue($submitValues, $assoc);
    }
}
