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

/**
 * Moodle selectors manager.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_partial_named_selector extends \Behat\Mink\Selector\PartialNamedSelector {

    /**
     * Creates selector instance.
     */
    public function __construct() {
        foreach (self::$customselectors as $alias => $selectors) {
            $this->registerNamedXpath($alias, implode(' | ', $selectors));
        }

        foreach (static::$moodleselectors as $name => $xpath) {
            $this->registerNamedXpath($name, $xpath);
        }

        // Call the constructor after adding any new selector or replacement values.
        parent::__construct();
    }

    /**
     * @var Allowed types when using text selectors arguments.
     */
    protected static $allowedtextselectors = array(
        'activity' => 'activity',
        'block' => 'block',
        'css_element' => 'css_element',
        'dialogue' => 'dialogue',
        'fieldset' => 'fieldset',
        'list_item' => 'list_item',
        'message_area_region' => 'message_area_region',
        'message_area_region_content' => 'message_area_region_content',
        'question' => 'question',
        'region' => 'region',
        'section' => 'section',
        'table' => 'table',
        'table_row' => 'table_row',
        'xpath_element' => 'xpath_element',
        'form_row' => 'form_row',
    );

    /**
     * @var Allowed types when using selector arguments.
     */
    protected static $allowedselectors = array(
        'activity' => 'activity',
        'block' => 'block',
        'button' => 'button',
        'checkbox' => 'checkbox',
        'css_element' => 'css_element',
        'dialogue' => 'dialogue',
        'field' => 'field',
        'fieldset' => 'fieldset',
        'file' => 'file',
        'filemanager' => 'filemanager',
        'link' => 'link',
        'link_or_button' => 'link_or_button',
        'list_item' => 'list_item',
        'message_area_action' => 'message_area_action',
        'message_area_region' => 'message_area_region',
        'message_area_region_content' => 'message_area_region_content',
        'optgroup' => 'optgroup',
        'option' => 'option',
        'question' => 'question',
        'radio' => 'radio',
        'region' => 'region',
        'section' => 'section',
        'select' => 'select',
        'table' => 'table',
        'table_row' => 'table_row',
        'text' => 'text',
        'xpath_element' => 'xpath_element',
        'form_row' => 'form_row',
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
        'activity' => <<<XPATH
.//li[contains(concat(' ', normalize-space(@class), ' '), ' activity ')][normalize-space(.) = %locator% ]
XPATH
        , 'block' => <<<XPATH
.//*[@data-block][contains(concat(' ', normalize-space(@class), ' '), concat(' ', %locator%, ' ')) or
     descendant::*[self::h2|self::h3][normalize-space(.) = %locator%]  or
     @aria-label = %locator%]
XPATH
        , 'dialogue' => <<<XPATH
.//div[contains(concat(' ', normalize-space(@class), ' '), ' moodle-dialogue ') and
    normalize-space(descendant::div[
        contains(concat(' ', normalize-space(@class), ' '), ' moodle-dialogue-hd ')
        ]) = %locator%] |
.//div[contains(concat(' ', normalize-space(@class), ' '), ' yui-dialog ') and
    normalize-space(descendant::div[@class='hd']) = %locator%]
XPATH
        , 'list_item' => <<<XPATH
.//li[contains(normalize-space(.), %locator%) and not(.//li[contains(normalize-space(.), %locator%)])]
XPATH
        , 'question' => <<<XPATH
.//div[contains(concat(' ', normalize-space(@class), ' '), ' que ')]
    [contains(div[@class='content']/div[contains(concat(' ', normalize-space(@class), ' '), ' formulation ')], %locator%)]
XPATH
        , 'region' => <<<XPATH
.//*[self::div | self::section | self::aside | self::header | self::footer][./@id = %locator%]
XPATH
        , 'section' => <<<XPATH
.//li[contains(concat(' ', normalize-space(@class), ' '), ' section ')][./descendant::*[self::h3]
    [normalize-space(.) = %locator%][contains(concat(' ', normalize-space(@class), ' '), ' sectionname ') or
    contains(concat(' ', normalize-space(@class), ' '), ' section-title ')]] |
.//div[contains(concat(' ', normalize-space(@class), ' '), ' sitetopic ')]
    [./descendant::*[self::h2][normalize-space(.) = %locator%] or %locator% = 'frontpage']
XPATH
        , 'table' => <<<XPATH
.//table[(./@id = %locator% or contains(.//caption, %locator%) or contains(.//th, %locator%) or contains(concat(' ', normalize-space(@class), ' '), %locator% ))]
XPATH
        , 'table_row' => <<<XPATH
.//tr[contains(normalize-space(.), %locator%) and not(.//tr[contains(normalize-space(.), %locator%)])]
XPATH
        , 'text' => <<<XPATH
.//*[contains(., %locator%) and not(.//*[contains(., %locator%)])]
XPATH
        , 'form_row' => <<<XPATH
.//*[self::label or self::div[contains(concat(' ', @class, ' '), ' fstaticlabel ')]][contains(., %locator%)]/ancestor::*[contains(concat(' ', @class, ' '), ' fitem ')]
XPATH
        , 'message_area_region' => <<<XPATH
.//div[@data-region='messaging-area']/descendant::*[@data-region = %locator%]
XPATH
        , 'message_area_region_content' => <<<XPATH
.//div[@data-region='messaging-area']/descendant::*[@data-region-content = %locator%]
XPATH
        , 'message_area_action' => <<<XPATH
.//div[@data-region='messaging-area']/descendant::*[@data-action = %locator%]
XPATH
    );

    protected static $customselectors = [
        'field' => [
            'upstream' => <<<XPATH
.//*
[%fieldFilterWithPlaceholder%][%notFieldTypeFilter%][%fieldMatchWithPlaceholder%]
|
.//label[%tagTextMatch%]//.//*[%fieldFilterWithPlaceholder%][%notFieldTypeFilter%]
|
.//*
[%fieldFilterWithoutPlaceholder%][%notFieldTypeFilter%][%fieldMatchWithoutPlaceholder%]
|
.//label[%tagTextMatch%]//.//*[%fieldFilterWithoutPlaceholder%][%notFieldTypeFilter%]
XPATH
        ,
            'filemanager' => <<<XPATH
.//*[@data-fieldtype = 'filemanager' or @data-fieldtype = 'filepicker']
    /descendant::input[@id = //label[contains(normalize-space(string(.)), %locator%)]/@for]
XPATH
        ,
             'passwordunmask' => <<<XPATH
.//*[@data-passwordunmask='wrapper']
    /descendant::input[@id = %locator% or @id = //label[contains(normalize-space(string(.)), %locator%)]/@for]
XPATH
        ],
    ];

    /**
     * Allowed selectors getter.
     *
     * @return array
     */
    public static function get_allowed_selectors() {
        return static::$allowedselectors;
    }

    /**
     * Allowed text selectors getter.
     *
     * @return array
     */
    public static function get_allowed_text_selectors() {
        return static::$allowedtextselectors;
    }
}
