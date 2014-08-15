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
 * Moodle-specific selectors.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle selectors manager.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_selectors {

    /**
     * @var Allowed types when using text selectors arguments.
     */
    protected static $allowedtextselectors = array(
        'dialogue' => 'dialogue',
        'block' => 'block',
        'region' => 'region',
        'table_row' => 'table_row',
        'table' => 'table',
        'fieldset' => 'fieldset',
        'css_element' => 'css_element',
        'xpath_element' => 'xpath_element'
    );

    /**
     * @var Allowed types when using selector arguments.
     */
    protected static $allowedselectors = array(
        'dialogue' => 'dialogue',
        'block' => 'block',
        'region' => 'region',
        'table_row' => 'table_row',
        'link' => 'link',
        'button' => 'button',
        'link_or_button' => 'link_or_button',
        'select' => 'select',
        'checkbox' => 'checkbox',
        'radio' => 'radio',
        'file' => 'file',
        'filemanager' => 'filemanager',
        'optgroup' => 'optgroup',
        'option' => 'option',
        'table' => 'table',
        'field' => 'field',
        'fieldset' => 'fieldset',
        'text' => 'text',
        'css_element' => 'css_element',
        'xpath_element' => 'xpath_element'
    );

    /**
     * Behat by default comes with XPath, CSS and named selectors,
     * named selectors are a mapping between names (like button) and
     * xpaths that represents that names and includes a placeholder that
     * will be replaced by the locator. These are Moodle's own xpaths.
     *
     * @var XPaths for moodle elements.
     */
    protected static $moodleselectors = array(
        'text' => <<<XPATH
//*[contains(., %locator%)][count(./descendant::*[contains(., %locator%)]) = 0]
XPATH
        , 'dialogue' => <<<XPATH
//div[contains(concat(' ', normalize-space(@class), ' '), ' moodle-dialogue ') and
    normalize-space(descendant::div[
        contains(concat(' ', normalize-space(@class), ' '), ' moodle-dialogue-hd ')
        ]) = %locator%] |
//div[contains(concat(' ', normalize-space(@class), ' '), ' yui-dialog ') and
    normalize-space(descendant::div[@class='hd']) = %locator%]
XPATH
        , 'block' => <<<XPATH
//div[contains(concat(' ', normalize-space(@class), ' '), ' block ') and
    (contains(concat(' ', normalize-space(@class), ' '), concat(' ', %locator%, ' ')) or
     descendant::h2[normalize-space(.) = %locator%] or
     @aria-label = %locator%)]
XPATH
        , 'region' => <<<XPATH
//*[self::div | self::section | self::aside | self::header | self::footer][./@id = %locator%]
XPATH
        , 'table_row' => <<<XPATH
.//tr[contains(normalize-space(.), %locator%)]
XPATH
        , 'filemanager' => <<<XPATH
//div[contains(concat(' ', normalize-space(@class), ' '), ' ffilemanager ')]
    /descendant::input[@id = //label[contains(normalize-space(string(.)), %locator%)]/@for]
XPATH
        , 'table' => <<<XPATH
.//table[(./@id = %locator% or contains(.//caption, %locator%) or contains(concat(' ', normalize-space(@class), ' '), %locator% ))]
XPATH
    );

    /**
     * Returns the behat selector and locator for a given moodle selector and locator
     *
     * @param string $selectortype The moodle selector type, which includes moodle selectors
     * @param string $element The locator we look for in that kind of selector
     * @param Session $session The Mink opened session
     * @return array Contains the selector and the locator expected by Mink.
     */
    public static function get_behat_selector($selectortype, $element, Behat\Mink\Session $session) {

        // CSS and XPath selectors locator is one single argument.
        if ($selectortype == 'css_element' || $selectortype == 'xpath_element') {
            $selector = str_replace('_element', '', $selectortype);
            $locator = $element;
        } else {
            // Named selectors uses arrays as locators including the type of named selector.
            $locator = array($selectortype, $session->getSelectorsHandler()->xpathLiteral($element));
            $selector = 'named';
        }

        return array($selector, $locator);
    }

    /**
     * Adds moodle selectors as behat named selectors.
     *
     * @param Session $session The mink session
     * @return void
     */
    public static function register_moodle_selectors(Behat\Mink\Session $session) {

        foreach (self::get_moodle_selectors() as $name => $xpath) {
            $session->getSelectorsHandler()->getSelector('named')->registerNamedXpath($name, $xpath);
        }
    }

    /**
     * Allowed selectors getter.
     *
     * @return array
     */
    public static function get_allowed_selectors() {
        return self::$allowedselectors;
    }

    /**
     * Allowed text selectors getter.
     *
     * @return array
     */
    public static function get_allowed_text_selectors() {
        return self::$allowedtextselectors;
    }

    /**
     * Moodle selectors attribute accessor.
     *
     * @return array
     */
    protected static function get_moodle_selectors() {
        return self::$moodleselectors;
    }
}
