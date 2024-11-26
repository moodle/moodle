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
 * @copyright  2020 G J Barnard
 * @author     G J Barnard -
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

/**
 * Admin settings page tabs.
 */
class admin_settingspage_tabs extends \theme_boost_admin_settingspage_tabs {
    /** @var int The branch this Adaptable is for. */
    protected $mbranch;

    /**
     * see admin_settingpage for details of this function
     *
     * @param string $name The internal name for this external page. Must be unique amongst all part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param int $mbranch The branch this Adaptable is for.
     * @param mixed $req_capability The role capability/permission a user must have to access this external page.
     *                              Defaults to 'moodle/site:config'.
     * @param boolean $hidden Is this external page hidden in admin tree block? Default false.
     * @param stdClass $context The context the page relates to.
     */
    public function __construct($name, $visiblename, $mbranch, $reqcapability = 'moodle/site:config',
        $hidden = false, $context = null) {
        $this->mbranch = $mbranch;
        return parent::__construct($name, $visiblename, $reqcapability, $hidden, $context);
    }

    /**
     * Generate the HTML output.
     *
     * @return string
     */
    public function output_html() {
        global $CFG, $OUTPUT;

        $activetab = optional_param('activetab', '', PARAM_TEXT);
        $context = ['tabs' => []];
        $havesetactive = false;

        foreach ($this->get_tabs() as $tab) {
            $active = false;

            // Default to first tab it not told otherwise.
            if (empty($activetab) && !$havesetactive) {
                $active = true;
                $havesetactive = true;
            } else if ($activetab === $tab->name) {
                $active = true;
            }

            $disabled = false;
            if ($tab instanceof admin_settingspage) {
                $disabled = $tab->get_disabled();
            }
            $context['tabs'][] = [
                'name' => $tab->name,
                'displayname' => $tab->visiblename,
                'html' => $tab->output_html(),
                'active' => $active,
                'disabled' => $disabled,
            ];
        }

        if (empty($context['tabs'])) {
            return '';
        }

        $themes = \core_plugin_manager::instance()->get_present_plugins('theme');
        if (!empty($themes['adaptable'])) {
            $plugininfo = $themes['adaptable'];
        } else {
            $plugininfo = \core_plugin_manager::instance()->get_plugin_info('theme_adaptable');
            $plugininfo->version = $plugininfo->versiondisk;
        }

        $context['versioninfo'] = get_string(
            'versioninfo',
            'theme_adaptable',
            [
                'moodle' => $CFG->release,
                'release' => $plugininfo->release,
                'version' => $plugininfo->version,
            ]
        );

        if (!empty($plugininfo->maturity)) {
            switch ($plugininfo->maturity) {
                case MATURITY_ALPHA:
                    $context['maturity'] = get_string('versionalpha', 'theme_adaptable');
                    $context['maturityalert'] = 'danger';
                    break;
                case MATURITY_BETA:
                    $context['maturity'] = get_string('versionbeta', 'theme_adaptable');
                    $context['maturityalert'] = 'danger';
                    break;
                case MATURITY_RC:
                    $context['maturity'] = get_string('versionrc', 'theme_adaptable');
                    $context['maturityalert'] = 'warning';
                    break;
                case MATURITY_STABLE:
                    $context['maturity'] = get_string('versionstable', 'theme_adaptable');
                    $context['maturityalert'] = 'info';
                    break;
            }
        }
        $context['privacynote'] = format_text(get_string('privacy:note', 'theme_adaptable'), FORMAT_MARKDOWN);

        if ($CFG->branch != $this->mbranch) {
            $context['versioncheck'] = 'Release ' . $plugininfo->release . ', version ' . $plugininfo->version .
                ' is incompatible with Moodle ' . $CFG->release;
            $context['versioncheck'] .= ', please get the correct version from ';
            $context['versioncheck'] .= '<a href="https://moodle.org/plugins/theme_adaptable" target="_blank">Moodle.org</a>.  ';
            $context['versioncheck'] .= 'If none is available, then please consider supporting the theme by funding it.  ';
            $context['versioncheck'] .= 'Please contact me via \'gjbarnard at gmail dot com\' or my ';
            $context['versioncheck'] .= '<a href="https://moodle.org/user/profile.php?id=442195">Moodle dot org profile</a>.  ';
            $context['versioncheck'] .= 'This is my <a href="https://about.me/gjbarnard">\'Web profile\'</a> if you want ';
            $context['versioncheck'] .= 'to know more about me.';
        }

        return $OUTPUT->render_from_template('theme_adaptable/adaptable_admin_setting_tabs', $context);
    }
}
