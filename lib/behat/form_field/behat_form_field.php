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
 * Generic moodleforms field.
 *
 * @package    core_form
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Session as Session,
    Behat\Mink\Element\NodeElement as NodeElement;

/**
 * Representation of a form field.
 *
 * Basically an interface with Mink session.
 *
 * @package    core_form
 * @category   test
 * @copyright  2012 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_form_field {

    /**
     * @var Session Behat session.
     */
    protected $session;

    /**
     * @var NodeElement The field DOM node to interact with.
     */
    protected $field;

    /**
     * General constructor with the node and the session to interact with.
     *
     * @param Session $session Reference to Mink session to traverse/modify the page DOM.
     * @param NodeElement $fieldnode The field DOM node
     * @return void
     */
    public function __construct(Session $session, NodeElement $fieldnode) {
        $this->session = $session;
        $this->field = $fieldnode;
    }

    /**
     * Sets the value to a field.
     *
     * @param string $value
     * @return void
     */
    public function set_value($value) {

        // If we are not dealing with a text-based tag try to find the most appropiate
        // behat_form_* class to deal with it.
        if ($instance = $this->guess_type()) {
            $instance->set_value($value);
        } else {
            $this->field->setValue($value);
        }
    }

    /**
     * Returns the current value of the select element.
     *
     * @return string
     */
    public function get_value() {

        // If we are not dealing with a text-based tag try to find the most appropiate
        // behat_form_* class to deal with it.
        if ($instance = $this->guess_type()) {
            return $instance->get_value();
        } else {
            return $this->field->getValue();
        }
    }

    /**
     * Guesses the element type we are dealing with in case is not a text-based element.
     *
     * This class is the generic field type, behat_field_manager::get_form_field()
     * should be able to find the appropiate class for the field type, but
     * in cases like moodle form group elements we can not find the type of
     * the field through the DOM so we also need to take care of the
     * different field types from here. If we need to deal with more complex
     * moodle form elements we will need to refactor this simple HTML elements
     * guess method.
     *
     * @return mixed False if no need for an special behat_form_*, otherwise the behat_form_*
     */
    private function guess_type() {
        global $CFG;

        // Textareas are considered text based elements.
        $tagname = strtolower($this->field->getTagName());
        if ($tagname == 'textarea') {

            if (!$this->running_javascript()) {
                return false;
            }

            // If there is an iframe with $id + _ifr there a TinyMCE editor loaded.
            $xpath = '//iframe[@id="' . $this->field->getAttribute('id') . '_ifr"]';
            if (!$this->session->getPage()->find('xpath', $xpath)) {

                // Generic one if it is a normal textarea.
                return false;
            }

            $classname = 'behat_form_editor';

        } else if ($tagname == 'input') {
            $type = $this->field->getAttribute('type');
            switch ($type) {
                case 'text':
                    return false;
                case 'checkbox':
                    $classname = 'behat_form_checkbox';
                    break;
                case 'radio':
                    $classname = 'behat_form_radio';
                    break;
                default:
                    return false;
            }

        } else if ($tagname == 'select') {
            // Select tag.
            $classname = 'behat_form_select';

        } else {
            // We can not provide a closer field type.
            return false;
        }

        $classpath = $CFG->dirroot . '/lib/behat/form_field/' . $classname . '.php';
        require_once($classpath);
        return new $classname($this->session, $this->field);
    }

    /**
     * Returns whether the scenario is running in a browser that can run Javascript or not.
     *
     * @return bool
     */
    protected function running_javascript() {
        return get_class($this->session->getDriver()) !== 'Behat\Mink\Driver\GoutteDriver';
    }

    /**
     * Gets the field internal id used by selenium wire protocol.
     *
     * Only available when running_javascript().
     *
     * @throws coding_exception
     * @return int
     */
    protected function get_internal_field_id() {

        if (!$this->running_javascript()) {
            throw new coding_exception('You can only get an internal ID using the selenium driver.');
        }

        return $this->session->
            getDriver()->
            getWebDriverSession()->
            element('xpath', $this->field->getXPath())->
            getID();
    }
}
