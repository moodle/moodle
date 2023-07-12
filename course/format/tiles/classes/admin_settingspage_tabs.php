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
 * format_tiles settings tabs
 *
 * @package     format_tiles
 * @copyright   2019 David Watson {@link http://evolutioncode.uk} based on Andreas Grabs <info@grabs-edv.de> (Unilabel plugin)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_tiles;

defined('MOODLE_INTERNAL') || die();
/**
 * Settings page providing a tabbed view.
 * @package     format_tiles
 * @copyright   2019 David Watson {@link http://evolutioncode.uk}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_settingspage_tabs extends \admin_settingpage {

    /** @var array $tabs The tabs of this page */
    protected $tabs = array();

    /**
     * Add a tab.
     *
     * @param \admin_settingpage $tab A tab.
     * @return bool
     */
    public function add_tab(\admin_settingpage $tab) {
        foreach ($tab->settings as $setting) {
            $this->settings->{$setting->plugin.$setting->name} = $setting;
        }
        $this->tabs[] = $tab;
        return true;
    }

    /**
     * Add a setting page as new tab.
     *
     * @param \admin_settingpage $tab
     * @return bool
     */
    public function add($tab) {
        return $this->add_tab($tab);
    }

    /**
     * Get tabs.
     *
     * @return array
     */
    public function get_tabs() {
        return $this->tabs;
    }

    /**
     * Generate the HTML output.
     *
     * @return string
     * @throws \dml_exception
     */
    public function output_html() {
        global $OUTPUT;

        $context = array('tabs' => array());

        foreach ($this->get_tabs() as $index => $tab) {
            $data = array(
                'name' => str_replace('format_tiles/' , '', $tab->name),
                'displayname' => $tab->visiblename,
                'html' => $tab->output_html()
            );
            if ($index == 0 ) {
                $data['active'] = 1;
            }
            if ($tab->name === "format_tiles/tab-colours") {
                $data['iscolourstab'] = 1;
            }

            $context['tabs'][] = $data;
        }
        $context['documentationurl'] = get_config('format_tiles', 'documentationurl');
        $context['showregisterbutton'] = !\format_tiles\registration_manager::is_registered()
            && !\format_tiles\registration_manager::has_recent_attempt();
        $context['sesskey'] = sesskey();

        return $OUTPUT->render_from_template('format_tiles/admin_setting_tabs', $context);
    }
}

