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
 * Course renderer.
 *
 * @package theme_academi
 * @copyright 2023 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author LMSACE Dev Team
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_academi\output;

use html_writer;
use moodle_url;
use custom_menu;

/**
 * The core course renderer.
 *
 * Can be retrieved with the following:
 * $renderer = $PAGE->get_renderer('core','course');
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /**
     * Returns the moodle_url for the favicon.
     *
     * This renderer function is copied and modified from /lib/outputrenderers.php
     *
     * @since Moodle 2.5.1 2.6
     * @return moodle_url The moodle_url for the favicon
     */
    public function favicon() {
        $logo = $this->image_url('favicon', 'theme');
        if (!empty($this->page->theme->settings->favicon)) {
            $logo = $this->page->theme->setting_file_url('favicon', 'favicon');
        } else {
            $logo = parent::favicon();
        }
        return $logo;
    }

    /**
     * Footer info links.
     * @return string
     */
    public function footer_infolinks() {
        $infolink = theme_academi_get_setting('infolink');
        $menu = new custom_menu($infolink, current_language());
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('theme_academi/custom_menu_item', $context);
        }
        return $content;
    }
}
