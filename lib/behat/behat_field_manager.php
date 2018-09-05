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
 * Form fields helper.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Session as Session,
    Behat\Mink\Element\NodeElement as NodeElement,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\MinkExtension\Context\RawMinkContext as RawMinkContext;

/**
 * Helper to interact with form fields.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_field_manager {

    /**
     * Gets an instance of the form field from it's label
     *
     * @param string $label
     * @param RawMinkContext $context
     * @return behat_form_field
     */
    public static function get_form_field_from_label($label, RawMinkContext $context) {

        // There are moodle form elements that are not directly related with
        // a basic HTML form field, we should also take care of them.
        // The DOM node.
        $fieldnode = $context->find_field($label);

        // The behat field manager.
        return self::get_form_field($fieldnode, $context->getSession());
    }

    /**
     * Gets an instance of the form field.
     *
     * Not all the fields are part of a moodle form, in this
     * cases it fallsback to the generic form field. Also note
     * that this generic field type is using a generic setValue()
     * method from the Behat API, which is not always good to set
     * the value of form elements.
     *
     * @param NodeElement $fieldnode
     * @param Session $session The behat browser session
     * @return behat_form_field
     */
    public static function get_form_field(NodeElement $fieldnode, Session $session) {

        // Get the field type if is part of a moodleform.
        if (self::is_moodleform_field($fieldnode)) {
            // This might go out of scope, finding element beyond the dom and fail. So fallback to guessing type.
            try {
                $type = self::get_field_node_type($fieldnode, $session);
            } catch (WebDriver\Exception\InvalidSelector $e) {
                $type = 'field';
            }
        }

        // If is not a moodleforms field use the base field type.
        if (empty($type)) {
            $type = 'field';
        }

        return self::get_field_instance($type, $fieldnode, $session);
    }

    /**
     * Returns the appropiate behat_form_field according to the provided type.
     *
     * It defaults to behat_form_field.
     *
     * @param string $type The field type (checkbox, date_selector, text...)
     * @param NodeElement $fieldnode
     * @param Session $session The behat session
     * @return behat_form_field
     */
    public static function get_field_instance($type, NodeElement $fieldnode, Session $session) {

        global $CFG;

        // If the field is not part of a moodleform, we should still try to find out
        // which field type are we dealing with.
        if ($type == 'field' &&
                $guessedtype = self::guess_field_type($fieldnode, $session)) {
            $type = $guessedtype;
        }

        $classname = 'behat_form_' . $type;

        // Fallsback on the type guesser if nothing specific exists.
        $classpath = $CFG->libdir . '/behat/form_field/' . $classname . '.php';
        if (!file_exists($classpath)) {
            $classname = 'behat_form_field';
            $classpath = $CFG->libdir . '/behat/form_field/' . $classname . '.php';
        }

        // Returns the instance.
        require_once($classpath);
        return new $classname($session, $fieldnode);
    }

    /**
     * Guesses a basic field type and returns it.
     *
     * This method is intended to detect HTML form fields when no
     * moodleform-specific elements have been detected.
     *
     * @param NodeElement $fieldnode
     * @param Session $session
     * @return string|bool The field type or false.
     */
    public static function guess_field_type(NodeElement $fieldnode, Session $session) {

        // Textareas are considered text based elements.
        $tagname = strtolower($fieldnode->getTagName());
        if ($tagname == 'textarea') {

            // If there is an iframe with $id + _ifr there a TinyMCE editor loaded.
            $xpath = '//div[@id="' . $fieldnode->getAttribute('id') . 'editable"]';
            if ($session->getPage()->find('xpath', $xpath)) {
                return 'editor';
            }
            return 'textarea';

        } else if ($tagname == 'input') {
            $type = $fieldnode->getAttribute('type');
            switch ($type) {
                case 'text':
                case 'password':
                case 'email':
                case 'file':
                    return 'text';
                case 'checkbox':
                    return 'checkbox';
                    break;
                case 'radio':
                    return 'radio';
                    break;
                default:
                    // Here we return false because all text-based
                    // fields should be included in the first switch case.
                    return false;
            }

        } else if ($tagname == 'select') {
            // Select tag.
            return 'select';
        }

        // We can not provide a closer field type.
        return false;
    }

    /**
     * Detects when the field is a moodleform field type.
     *
     * Note that there are fields inside moodleforms that are not
     * moodleform element; this method can not detect this, this will
     * be managed by get_field_node_type, after failing to find the form
     * element element type.
     *
     * @param NodeElement $fieldnode
     * @return bool
     */
    protected static function is_moodleform_field(NodeElement $fieldnode) {

        // We already waited when getting the NodeElement and we don't want an exception if it's not part of a moodleform.
        $parentformfound = $fieldnode->find('xpath',
            "/ancestor::form[contains(concat(' ', normalize-space(@class), ' '), ' mform ')]"
        );

        return ($parentformfound != false);
    }

    /**
     * Recursive method to find the field type.
     *
     * Depending on the field the felement class node is in a level or in another. We
     * look recursively for a parent node with a 'felement' class to find the field type.
     *
     * @param NodeElement $fieldnode The current node.
     * @param Session $session The behat browser session
     * @return mixed A NodeElement if we continue looking for the element type and String or false when we are done.
     */
    protected static function get_field_node_type(NodeElement $fieldnode, Session $session) {

        // Special handling for availability field which requires custom JavaScript.
        if ($fieldnode->getAttribute('name') === 'availabilityconditionsjson') {
            return 'availability';
        }

        if ($fieldnode->getTagName() == 'html') {
            return false;
        }

        // If the type is explictly set on the element pointed to by the label - use it.
        if ($type = $fieldnode->getParent()->getAttribute('data-fieldtype')) {
            if ($type == 'tags') {
                return 'autocomplete';
            }
            return $type;
        }

        if (!empty($fieldnode->find('xpath', '/ancestor::*[@data-passwordunmaskid]'))) {
            return 'passwordunmask';
        }

        // We look for a parent node with 'felement' class.
        if ($class = $fieldnode->getParent()->getAttribute('class')) {

            if (strstr($class, 'felement') != false) {
                // Remove 'felement f' from class value.
                return substr($class, 10);
            }

            // Stop propagation through the DOM, if it does not have a felement is not part of a moodle form.
            if (strstr($class, 'fcontainer') != false) {
                return false;
            }
        }

        return self::get_field_node_type($fieldnode->getParent(), $session);
    }

    /**
     * Gets an instance of the form field.
     *
     * Not all the fields are part of a moodle form, in this
     * cases it fallsback to the generic form field. Also note
     * that this generic field type is using a generic setValue()
     * method from the Behat API, which is not always good to set
     * the value of form elements.
     *
     * @deprecated since Moodle 2.6 MDL-39634 - please do not use this function any more.
     * @todo MDL-XXXXX This will be deleted in Moodle 2.8
     * @see behat_field_manager::get_form_field()
     * @param NodeElement $fieldnode
     * @param string $locator
     * @param Session $session The behat browser session
     * @return behat_form_field
     */
    public static function get_field(NodeElement $fieldnode, $locator, Session $session) {
        debugging('Function behat_field_manager::get_field() is deprecated, ' .
            'please use function behat_field_manager::get_form_field() instead', DEBUG_DEVELOPER);

        return self::get_form_field($fieldnode, $session);
    }

    /**
     * Recursive method to find the field type.
     *
     * Depending on the field the felement class node is in a level or in another. We
     * look recursively for a parent node with a 'felement' class to find the field type.
     *
     * @deprecated since Moodle 2.6 MDL-39634 - please do not use this function any more.
     * @todo MDL-XXXXX This will be deleted in Moodle 2.8
     * @see behat_field_manager::get_field_node_type()
     * @param NodeElement $fieldnode The current node.
     * @param string $locator
     * @param Session $session The behat browser session
     * @return mixed A NodeElement if we continue looking for the element type and String or false when we are done.
     */
    protected static function get_node_type(NodeElement $fieldnode, $locator, Session $session) {
        debugging('Function behat_field_manager::get_node_type() is deprecated, ' .
            'please use function behat_field_manager::get_field_node_type() instead', DEBUG_DEVELOPER);

        return self::get_field_node_type($fieldnode, $session);
    }
}
