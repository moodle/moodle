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

use core\exception\moodle_exception;
use moodle_url;

/**
 * Custom menu class
 *
 * This class is used to operate a custom menu that can be rendered for the page.
 * The custom menu is built using $CFG->custommenuitems and is a structured collection
 * of custom_menu_item nodes that can be rendered by the core renderer.
 *
 * To configure the custom menu:
 *     Settings: Administration > Appearance > Advanced theme settings
 *
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class custom_menu extends custom_menu_item {
    /**
     * @var string The language we should render for, null disables multilang support.
     */
    protected $currentlanguage = null;

    /**
     * Creates the custom menu
     *
     * @param string $definition the menu items definition in syntax required by {@see convert_text_to_menu_nodes()}
     * @param string $currentlanguage the current language code, null disables multilang support
     */
    public function __construct($definition = '', $currentlanguage = null) {
        $this->currentlanguage = $currentlanguage;
        parent::__construct('root'); // create virtual root element of the menu
        if (!empty($definition)) {
            $this->override_children(self::convert_text_to_menu_nodes($definition, $currentlanguage));
        }
    }

    /**
     * Overrides the children of this custom menu. Useful when getting children
     * from $CFG->custommenuitems
     *
     * @param array $children
     */
    public function override_children(array $children) {
        $this->children = [];
        foreach ($children as $child) {
            if ($child instanceof custom_menu_item) {
                $this->children[] = $child;
            }
        }
    }

    /**
     * Converts a string into a structured array of custom_menu_items which can
     * then be added to a custom menu.
     *
     * Structure:
     *     text|url|title|langs
     * The number of hyphens at the start determines the depth of the item. The
     * languages are optional, comma separated list of languages the line is for.
     *
     * Example structure:
     *     First level first item|http://www.moodle.com/
     *     -Second level first item|http://www.moodle.com/partners/
     *     -Second level second item|http://www.moodle.com/hq/
     *     --Third level first item|http://www.moodle.com/jobs/
     *     -Second level third item|http://www.moodle.com/development/
     *     First level second item|http://www.moodle.com/feedback/
     *     First level third item
     *     English only|http://moodle.com|English only item|en
     *     German only|http://moodle.de|Deutsch|de,de_du,de_kids
     *
     *
     * @static
     * @param string $text the menu items definition
     * @param string $language the language code, null disables multilang support
     * @return array
     */
    public static function convert_text_to_menu_nodes($text, $language = null) {
        $root = new custom_menu();
        $lastitem = $root;
        $lastdepth = 0;
        $hiddenitems = [];
        $lines = explode("\n", $text);
        foreach ($lines as $linenumber => $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            // Parse item settings.
            $itemtext = null;
            $itemurl = null;
            $itemtitle = null;
            $itemvisible = true;
            $settings = explode('|', $line);
            foreach ($settings as $i => $setting) {
                $setting = trim($setting);
                if ($setting !== '') {
                    switch ($i) {
                        case 0: // Menu text.
                            $itemtext = ltrim($setting, '-');
                            break;
                        case 1: // URL.
                            try {
                                $itemurl = new moodle_url($setting);
                            } catch (moodle_exception $exception) {
                                // We're not actually worried about this, we don't want to mess up the display
                                // just for a wrongly entered URL.
                                $itemurl = null;
                            }
                            break;
                        case 2: // Title attribute.
                            $itemtitle = $setting;
                            break;
                        case 3: // Language.
                            if (!empty($language)) {
                                $itemlanguages = array_map('trim', explode(',', $setting));
                                $itemvisible &= in_array($language, $itemlanguages);
                            }
                            break;
                    }
                }
            }
            // Get depth of new item.
            preg_match('/^(\-*)/', $line, $match);
            $itemdepth = strlen($match[1]) + 1;
            // Find parent item for new item.
            while (($lastdepth - $itemdepth) >= 0) {
                $lastitem = $lastitem->get_parent();
                $lastdepth--;
            }
            $lastitem = $lastitem->add($itemtext, $itemurl, $itemtitle, $linenumber + 1);
            $lastdepth++;
            if (!$itemvisible) {
                $hiddenitems[] = $lastitem;
            }
        }
        foreach ($hiddenitems as $item) {
            $item->parent->remove_child($item);
        }
        return $root->get_children();
    }

    /**
     * Sorts two custom menu items
     *
     * This function is designed to be used with the usort method
     *     usort($this->children, array('custom_menu','sort_custom_menu_items'));
     *
     * @static
     * @param custom_menu_item $itema
     * @param custom_menu_item $itemb
     * @return int
     */
    public static function sort_custom_menu_items(custom_menu_item $itema, custom_menu_item $itemb) {
        $itema = $itema->get_sort_order();
        $itemb = $itemb->get_sort_order();
        if ($itema == $itemb) {
            return 0;
        }
        return ($itema > $itemb) ? +1 : -1;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(custom_menu::class, \custom_menu::class);
