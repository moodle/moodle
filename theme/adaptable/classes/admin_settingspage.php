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
 * Adaptable theme.
 *
 * @package    theme_adaptable
 * @copyright  2023 G J Barnard
 * @author     G J Barnard -
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

/**
 * Adaptable admin_settingpage
 */
class admin_settingspage extends \admin_settingpage {
    /** @var bool disabled. */
    private $disabled = false;

    /**
     * see admin_settingpage for details of this mathod.
     *
     * @param string $name The internal name for this external page. Must be unique am  ongst ALL part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param int $local If the settings on the page require local_adaptable.
     * @param mixed $req_capability The role capability/permission a user must have to access this external page.
     *                              Defaults to 'moodle/site:config'.
     * @param boolean $hidden Is this external page hidden in admin tree block? Default false.
     * @param stdClass $context The context the page relates to.
     */
    public function __construct($name, $visiblename, $local = false, $reqcapability = 'moodle/site:config',
        $hidden = false, $context = null) {
        parent::__construct($name, $visiblename, $reqcapability, $hidden, $context);
        if (($local) && (\theme_adaptable\toolbox::get_local_toolbox() === false)) {
            $localadaptableheading = 'Sponsors only';
            $localadaptableheadingdesc = 'These settings and functionlity are available to sponsors only, '.
                'please see the \'Information\' tab.';

            $this->disabled = true;
            $this->add(new \admin_setting_heading(
                'theme_adaptable_sponsor'.$name,
                $localadaptableheading,
                format_text($localadaptableheadingdesc, FORMAT_MARKDOWN)
            ));
        }
    }

    /**
     * Returns the disabled state.
     *
     * return bool Disabled state.
     */
    public function get_disabled() {
        return $this->disabled;
    }
}
