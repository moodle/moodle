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

    // Use the named selector trait.
    use behat_named_selector;

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

        foreach (self::$customreplacements as $from => $tos) {
            $this->registerReplacement($from, implode(' or ', $tos));
        }

        $this->registerReplacement('%iconMatch%', "(contains(concat(' ', @class, ' '), ' icon ') or self::img)");
        $this->registerReplacement('%imgAltMatch%', './/*[%iconMatch% and (%altMatch% or %titleMatch%)]');
        parent::__construct();
    }

    /**
     * @var array Allowed types when using text selectors arguments.
     */
    protected static $allowedtextselectors = array(
        'activity' => 'activity',
        'block' => 'block',
        'css_element' => 'css_element',
        'dialogue' => 'dialogue',
        'dropdown_item' => 'dropdown_item',
        'fieldset' => 'fieldset',
        'icon' => 'icon',
        'list_item' => 'list_item',
        'question' => 'question',
        'region' => 'region',
        'section' => 'section',
        'table' => 'table',
        'table_row' => 'table_row',
        'xpath_element' => 'xpath_element',
        'form_row' => 'form_row',
        'group_message_header' => 'group_message_header',
        'group_message' => 'group_message',
        'autocomplete' => 'autocomplete',
        'iframe' => 'iframe',
        'option_role' => 'option_role',
    );

    /**
     * @var array Allowed types when using selector arguments.
     */
    protected static $allowedselectors = array(
        'activity' => 'activity',
        'actionmenu' => 'actionmenu',
        'badge' => 'badge',
        'block' => 'block',
        'button' => 'button',
        'checkbox' => 'checkbox',
        'combobox' => 'combobox',
        'css_element' => 'css_element',
        'dialogue' => 'dialogue',
        'dropdown' => 'dropdown',
        'dropdown_item' => 'dropdown_item',
        'field' => 'field',
        'fieldset' => 'fieldset',
        'file' => 'file',
        'filemanager' => 'filemanager',
        'group_message' => 'group_message',
        'group_message_conversation' => 'group_message_conversation',
        'group_message_header' => 'group_message_header',
        'group_message_member' => 'group_message_member',
        'group_message_tab' => 'group_message_tab',
        'group_message_list_area' => 'group_message_list_area',
        'group_message_message_content' => 'group_message_message_content',
        'heading' => 'heading',
        'icon_container' => 'icon_container',
        'icon' => 'icon',
        'link' => 'link',
        'link_or_button' => 'link_or_button',
        'list_item' => 'list_item',
        'menuitem' => 'menuitem',
        'optgroup' => 'optgroup',
        'option' => 'option',
        'option_role' => 'option_role',
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
        'autocomplete_selection' => 'autocomplete_selection',
        'autocomplete_suggestions' => 'autocomplete_suggestions',
        'autocomplete' => 'autocomplete',
        'iframe' => 'iframe',
    );

    /**
     * Behat by default comes with XPath, CSS and named selectors,
     * named selectors are a mapping between names (like button) and
     * xpaths that represents that names and includes a placeholder that
     * will be replaced by the locator. These are Moodle's own xpaths.
     *
     * @var array XPaths for moodle elements.
     */
    protected static $moodleselectors = array(
        'activity' => <<<XPATH
.//li[contains(concat(' ', normalize-space(@class), ' '), ' activity ')][descendant::*[contains(normalize-space(.), %locator%)]]
XPATH
        , 'actionmenu' => <<<XPATH
.//*[
    contains(concat(' ', normalize-space(@class), ' '), ' action-menu ')
        and
    descendant::*[
        contains(concat(' ', normalize-space(@class), ' '), ' dropdown-toggle ')
            and
        (contains(normalize-space(.), %locator%) or descendant::*[%titleMatch%])
    ]
]
XPATH
        , 'badge' => <<<XPATH
.//*[self::span or self::button][(contains(@class, 'badge')) and text()[contains(., %locator%)]]
XPATH
        , 'block' => <<<XPATH
.//*[@data-block][contains(concat(' ', normalize-space(@class), ' '), concat(' ', %locator%, ' ')) or
     descendant::*[self::h2|self::h3|self::h4|self::h5][normalize-space(.) = %locator%]  or
     @aria-label = %locator%]
XPATH
        , 'combobox' => <<<XPATH
.//*[@role='combobox'][%titleMatch% or %ariaLabelMatch% or text()[contains(., %locator%)]]
XPATH
        , 'dialogue' => <<<XPATH
.//div[contains(concat(' ', normalize-space(@class), ' '), ' moodle-dialogue ') and
    not(contains(concat(' ', normalize-space(@class), ' '), ' moodle-dialogue-hidden ')) and
    normalize-space(descendant::div[
        contains(concat(' ', normalize-space(@class), ' '), ' moodle-dialogue-hd ')
        ]) = %locator%] |
.//div[contains(concat(' ', normalize-space(@class), ' '), ' yui-dialog ') and
    normalize-space(descendant::div[@class='hd']) = %locator%]
        |
.//div[@data-region='modal' and descendant::*[@data-region='title'] = %locator%]
        |
.//div[
        contains(concat(' ', normalize-space(@class), ' '), ' modal-content ')
            and
        normalize-space(descendant::*[self::h4 or self::h5][contains(concat(' ', normalize-space(@class), ' '), ' modal-title ')]) = %locator%
    ]
        |
.//div[
        contains(concat(' ', normalize-space(@class), ' '), ' modal ')
            and
        normalize-space(descendant::*[contains(concat(' ', normalize-space(@class), ' '), ' modal-header ')]) = %locator%
    ]
XPATH
    , 'dropdown' => <<<XPATH
        .//*[
            contains(concat(' ', normalize-space(@class), ' '), ' dropdown-menu ')
                and
            @aria-labelledby =
                (//*[
                        contains(concat(' ', normalize-space(@class), ' '), ' dropdown-toggle ')
                            and
                        (contains(normalize-space(.), %locator%) or descendant::*[%titleMatch%])
                ]/@id)
        ]
XPATH
    , 'dropdown_item' => <<<XPATH
        .//*[
            @role = 'listitem'
                and
            (contains(normalize-space(.), %locator%) or descendant::*[%titleMatch%])
        ]
XPATH
        , 'group_message' => <<<XPATH
        .//*[@data-conversation-id]//img[contains(@alt, %locator%)]/..
XPATH
        , 'group_message_conversation' => <<<XPATH
            .//*[@data-region='message-drawer' and contains(., %locator%)]//div[@data-region='content-message-container']
XPATH
    , 'group_message_header' => <<<XPATH
        .//*[@data-region='message-drawer']//div[@data-region='header-content' and contains(., %locator%)]
XPATH
    , 'group_message_member' => <<<XPATH
        .//*[@data-region='message-drawer']//div[@data-region='group-info-content-container']
        //div[@class='list-group' and not(contains(@class, 'hidden'))]//*[text()[contains(., %locator%)]] |
        .//*[@data-region='message-drawer']//div[@data-region='group-info-content-container']
        //div[@data-region='empty-message-container' and not(contains(@class, 'hidden')) and contains(., %locator%)]
XPATH
    , 'group_message_tab' => <<<XPATH
        .//*[@data-region='message-drawer']//button[@data-toggle='collapse' and contains(string(), %locator%)]
XPATH
    , 'group_message_list_area' => <<<XPATH
        .//*[@data-region='message-drawer']//*[contains(@data-region, concat('view-overview-', %locator%))]
XPATH
    , 'group_message_message_content' => <<<XPATH
        .//*[@data-region='message-drawer']//*[@data-region='message' and @data-message-id and contains(., %locator%)]
XPATH
    , 'heading' => <<<XPATH
        .//*[self::h1 or self::h2 or self::h3 or self::h4 or self::h5 or self::h6][contains(normalize-space(.), %locator%)]
XPATH
    , 'icon_container' => <<<XPATH
        .//span[contains(@data-region, concat(%locator%,'-icon-container'))]
XPATH
        , 'icon' => <<<XPATH
.//*[contains(concat(' ', normalize-space(@class), ' '), ' icon ') and ( contains(normalize-space(@title), %locator%))]
XPATH
        , 'list_item' => <<<XPATH
.//li[contains(normalize-space(.), %locator%) and not(.//li[contains(normalize-space(.), %locator%)])]
XPATH
        , 'menuitem' => <<<XPATH
.//*[@role='menuitem'][%titleMatch% or %ariaLabelMatch% or text()[contains(., %locator%)]]
XPATH
    , 'option_role' => <<<XPATH
.//*[@role='option'][%titleMatch% or %ariaLabelMatch% or text()[contains(., %locator%)]] |
.//*[@role='option']/following-sibling::label[contains(., %locator%)]/preceding-sibling::input
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
.//*[contains(concat(' ', @class, ' '), ' col-form-label ')]
    [normalize-space(.)= %locator%]
    /ancestor::*[contains(concat(' ', @class, ' '), ' fitem ')]
XPATH
        , 'autocomplete_selection' => <<<XPATH
.//div[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'form-autocomplete-selection', ' '))]/span[@role='option'][contains(normalize-space(.), %locator%)]
XPATH
        , 'autocomplete_suggestions' => <<<XPATH
.//ul[contains(concat(' ', normalize-space(@class), ' '), concat(' ', 'form-autocomplete-suggestions', ' '))]/li[@role='option'][contains(normalize-space(.), %locator%)]
XPATH
        , 'autocomplete' => <<<XPATH
.//descendant::input[@id = //label[contains(normalize-space(string(.)), %locator%)]/@for]/ancestor::*[@data-fieldtype = 'autocomplete']
XPATH
        , 'iframe' => <<<XPATH
.//iframe[(%idOrNameMatch% or (contains(concat(' ', normalize-space(@class), ' '), %locator% )))]
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
    /descendant::input[@id = substring-before(//p[contains(normalize-space(string(.)), %locator%)]/@id, '_label')]
XPATH
        ,
             'passwordunmask' => <<<XPATH
.//*[@data-passwordunmask='wrapper']
    /descendant::input[@id = %locator% or @id = //label[contains(normalize-space(string(.)), %locator%)]/@for]
XPATH
        ,
             'inplaceeditable' => <<<XPATH
.//descendant::span[@data-inplaceeditable][descendant::a[%titleMatch%]]
XPATH
        ,
            'date_time' => <<<XPATH
.//fieldset[(%idMatch% or ./legend[%exactTagTextMatch%]) and (@data-fieldtype='date' or @data-fieldtype='date_time')]
XPATH
        ,
            'select_menu' => <<<XPATH
//*[@role='combobox'][@aria-labelledby = //label[contains(normalize-space(string(.)), %locator%)]/@id]
XPATH
        ,
        ],
    ];

    /**
     * Mink comes with a number of named replacements.
     * Sometimes we want to add our own.
     *
     * @var array XPaths for moodle elements.
     */
    protected static $customreplacements = [
        '%buttonMatch%' => [
            'upstream' => '%idOrNameMatch% or %valueMatch% or %titleMatch%',
            'aria' => '%ariaLabelMatch%',
        ],
        '%ariaLabelMatch%' => [
            'moodle' => 'contains(./@aria-label, %locator%)',
        ],
        '%exactTagTextMatch%' => [
            // This is based upon the upstream tagTextMatch but performs an exact match rather than a loose match using
            // contains().
            // If possible we should only use exact matches for any new form fields that we add.
            'moodle' => 'normalize-space(text())=%locator%',
        ],
    ];

    /** @var List of deprecated selectors */
    protected static $deprecatedselectors = [
        'group_message' => 'core_message > Message',
        'group_message_member' => 'core_message > Message member',
        'group_message_tab' => 'core_message > Message tab',
        'group_message_list_area' => 'core_message > Message list area',
        'group_message_message_content' => 'core_message > Message content',
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
