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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

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
class behat_form_field implements behat_session_interface {

    // All of the functionality of behat_base is shared with form fields via the behat_session_trait trait.
    use behat_session_trait;

    /**
     * @var Session Behat session.
     */
    protected $session;

    /**
     * @var NodeElement The field DOM node to interact with.
     */
    protected $field;

    /**
     * @var string The field's locator.
     */
    protected $fieldlocator = false;

    /**
     * Returns the Mink session.
     *
     * @param   string|null $name name of the session OR active session will be used
     * @return  \Behat\Mink\Session
     */
    public function getSession($name = null) {
        return $this->session;
    }


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
        // We delegate to the best guess, if we arrived here
        // using the generic behat_form_field is because we are
        // dealing with a fgroup element.
        $instance = $this->guess_type();
        return $instance->set_value($value);
    }

    /**
     * Returns the current value of the select element.
     *
     * @return string
     */
    public function get_value() {
        // We delegate to the best guess, if we arrived here
        // using the generic behat_form_field is because we are
        // dealing with a fgroup element.
        $instance = $this->guess_type();
        return $instance->get_value();
    }

    /**
     * Presses specific keyboard key.
     *
     * @param mixed  $char     could be either char ('b') or char-code (98)
     * @param string $modifier keyboard modifier (could be 'ctrl', 'alt', 'shift' or 'meta')
     */
    public function key_press($char, $modifier = null) {
        // We delegate to the best guess, if we arrived here
        // using the generic behat_form_field is because we are
        // dealing with a fgroup element.
        $instance = $this->guess_type();
        $instance->field->keyDown($char, $modifier);
        try {
            $instance->field->keyPress($char, $modifier);
            $instance->field->keyUp($char, $modifier);
        } catch (\Facebook\WebDriver\Exception\WebDriverException $e) {
            // If the JS handler attached to keydown or keypress destroys the element
            // the later events may trigger errors because form element no longer exist
            // or is not visible. Ignore such exceptions here.
        } catch (\Behat\Mink\Exception\ElementNotFoundException $e) {
            // Other Mink drivers can throw this for the same reason as above.
        }
    }

    /**
     * Generic match implementation
     *
     * Will work well with text-based fields, extension required
     * for most of the other cases.
     *
     * @param string $expectedvalue
     * @return bool The provided value matches the field value?
     */
    public function matches($expectedvalue) {
        // We delegate to the best guess, if we arrived here
        // using the generic behat_form_field is because we are
        // dealing with a fgroup element.
        $instance = $this->guess_type();
        return $instance->matches($expectedvalue);
    }

    /**
     * Get the value of an attribute set on this field.
     *
     * @param string $name The attribute name
     * @return string The attribute value
     */
    public function get_attribute($name) {
        return $this->field->getAttribute($name);
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
     * @return behat_form_field
     */
    private function guess_type() {
        return $this->get_field_instance_for_element($this->field);
    }

    /**
     * Returns the appropriate form field object for a given node element.
     *
     * @param NodeElement $element The node element
     * @return behat_form_field
     */
    protected function get_field_instance_for_element(NodeElement $element): behat_form_field {
        global $CFG;

        // We default to the text-based field if nothing was detected.
        if (!$type = behat_field_manager::guess_field_type($element, $this->session)) {
            $type = 'text';
        }

        $classname = 'behat_form_' . $type;
        $classpath = $CFG->dirroot . '/lib/behat/form_field/' . $classname . '.php';
        require_once($classpath);

        return new $classname($this->session, $element);
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
     * Waits for all the JS activity to be completed.
     *
     * @return bool Whether any JS is still pending completion.
     */
    protected function wait_for_pending_js() {
        if (!$this->running_javascript()) {
            // JS is not available therefore there is nothing to wait for.
            return false;
        }

        return behat_base::wait_for_pending_js_in_session($this->session);
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

        return $this->getSession()
            ->getDriver()
            ->getWebDriver()
            ->findElement(WebDriverBy::xpath($node->getXpath()))
            ->getID();
    }

    /**
     * Checks if the provided text matches the field value.
     *
     * @param string $expectedvalue
     * @return bool
     */
    protected function text_matches($expectedvalue) {
        // Non strict string comparison.
        if (trim($expectedvalue) == trim($this->get_value())) {
            return true;
        }

        // Do one more matching attempt for floats that are valid with current decsep in use
        // (let's continue non strict comparing them as strings, but once unformatted).
        $expectedfloat = unformat_float(trim($expectedvalue), true);
        $actualfloat = unformat_float(trim($this->get_value()), true);
        // If they aren't null or false, then we are good to be compared (basically is_numeric()).
        $goodfloats = !is_null($expectedfloat) && ($expectedfloat !== false) &&
            !is_null($actualfloat) && ($actualfloat !== false);
        if ($goodfloats && ((string)$expectedfloat == (string)$actualfloat)) {
            return true;
        }

        return false;
    }

    /**
     * Gets the field locator.
     *
     * Defaults to the field label but you can
     * specify other locators if you are interested.
     *
     * Public visibility as in most cases will be hard to
     * use this method in a generic way, as fields can
     * be selected using multiple ways (label, id, name...).
     *
     * @throws coding_exception
     * @param string $locatortype
     * @return string
     */
    protected function get_field_locator($locatortype = false) {

        if (!empty($this->fieldlocator)) {
            return $this->fieldlocator;
        }

        $fieldid = $this->field->getAttribute('id');

        // Defaults to label.
        if ($locatortype == 'label' || $locatortype == false) {

            $labelnode = $this->session->getPage()->find('xpath', "//label[@for='$fieldid']|//p[@id='{$fieldid}_label']");

            // Exception only if $locatortype was specified.
            if (!$labelnode && $locatortype == 'label') {
                throw new coding_exception('Field with "' . $fieldid . '" id does not have a label.');
            }

            $this->fieldlocator = $labelnode->getText();
        }

        // Let's look for the name as a second option (more popular than
        // id's when pointing to fields).
        if (($locatortype == 'name' || $locatortype == false) &&
                empty($this->fieldlocator)) {

            $name = $this->field->getAttribute('name');

            // Exception only if $locatortype was specified.
            if (!$name && $locatortype == 'name') {
                throw new coding_exception('Field with "' . $fieldid . '" id does not have a name attribute.');
            }

            $this->fieldlocator = $name;
        }

        // Otherwise returns the id if no specific locator type was provided.
        if (empty($this->fieldlocator)) {
            $this->fieldlocator = $fieldid;
        }

        return $this->fieldlocator;
    }
}
