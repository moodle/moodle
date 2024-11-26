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
 * Adaptable's custom menu.
 *
 * @package    theme_adaptable
 * @copyright  2024 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable\output;

use core\output\custom_menu_item;
use core\url;

/**
 * Adaptable's custom menu.
 */
class custom_menu extends \custom_menu {
    /**
     * Creates the custom menu
     *
     * @param string $definition the menu items definition in syntax required by {@link convert_text_to_menu_nodes()}
     * @param string $currentlanguage the current language code, null disables multilang support
     */
    public function __construct($definition = '', $currentlanguage = null) {
        $this->currentlanguage = $currentlanguage;
        custom_menu_item::__construct('root'); // create virtual root element of the menu
        if (!empty($definition)) {
            $this->override_children(self::convert_text_to_menu_nodes($definition, $currentlanguage));
        }
    }

    /**
     * Adds the custom menu items to this or another menu.
     *
     * @param string $definition The menu items definition in syntax required by {@link convert_text_to_menu_nodes()}.
     * @param string $currentlanguage The current language code, null disables multilang support.
     * @param string $menu Other menu to add to (optional).
     */
    public function add_custom_menu_items($definition = '', $currentlanguage = null, custom_menu_item $menu = null) {
        if (!empty($definition)) {
            $items = self::convert_text_to_menu_nodes($definition, $currentlanguage);
            if (empty($menu)) {
                $this->currentlanguage = $currentlanguage;
                foreach ($items as $item) {
                    $sort = $this->lastsort + 1;
                    $item->sort = (int)$sort;
                    $this->lastsort = (int)$sort;
                }
                $this->children = array_merge($this->children, $items);
            } else {
                $menu->currentlanguage = $currentlanguage;
                $menu->children = array_merge($menu->children, $items);
            }
        }
    }

    /**
     * Converts a string into a structured array of custom_menu_items which can
     * then be added to a custom menu.
     *
     * Structure:
     *     text|url|title|langs|fontawesome classes or name|capability.
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
     * @param string $text the menu items definition.
     * @param string $language the language code, null disables multilang support.
     * @return array Of custom_menu_item instances.
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
            $itemfa = null;
            $itemshown = true;
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
                                $itemurl = new url($setting);
                            } catch (\moodle_exception $exception) {
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
                        case 4: // Font awesome icon class.
                            $itemfa = $setting;
                            break;
                        case 5: // Capability.
                            global $PAGE;
                            /* Potential capability.  Based upon work by..
                             * @copyright 2018 Mathieu Domingo
                             * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
                             */
                            $setting = trim($setting);
                            if (get_capability_info($setting)) {
                                // Valid capability.
                                if (!has_capability($setting, $PAGE->context)) {
                                    $itemshown = false;
                                }
                            }
                        break;
                    }
                }
            }
            if ($itemshown) {
                // Get depth of new item.
                preg_match('/^(\-*)/', $line, $match);
                $itemdepth = strlen($match[1]) + 1;
                // Find parent item for new item.
                while (($lastdepth - $itemdepth) >= 0) {
                    $lastitem = $lastitem->get_parent();
                    $lastdepth--;
                }
                if ($itemfa) {
                    $itemtext = \theme_adaptable\toolbox::getfontawesomemarkup($itemfa, ['mr-1']) . $itemtext;
                }
                $lastitem = $lastitem->add($itemtext, $itemurl, $itemtitle, $linenumber + 1);
                $lastdepth++;
                if (!$itemvisible) {
                    $hiddenitems[] = $lastitem;
                }
            }
        }
        foreach ($hiddenitems as $item) {
            $item->parent->remove_child($item);
        }
        return $root->get_children();
    }
}
