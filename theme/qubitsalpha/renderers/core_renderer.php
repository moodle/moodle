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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_qubitsalpha
 * @copyright  2023 Qubits Dev Team.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class theme_qubitsalpha_core_renderer extends theme_boost\output\core_renderer {

    public function brand_color_code()
    {
        global $CFG;
        $osettings = $CFG->cursitesettings;
        $tltmp_txt = array(
            'primary_color' => $osettings->color1,
            'secondary_color' => $osettings->color2              
        );
        return $this->render_from_template('theme_qubitsalpha/custom/brandstylecode', $tltmp_txt);
    }

    public function get_site_name(){
        global $CFG, $SITE;
        $sitename = format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]);
        if((SITE_MAIN_DOMAIN != $_SERVER['HTTP_HOST']))
            $sitename = $CFG->cursitesettings->name;
        return $sitename;
    }

    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        global $CFG;
        if((SITE_MAIN_DOMAIN != $_SERVER['HTTP_HOST'])){
            $domainname = str_replace(".", "", $_SERVER['HTTP_HOST']);
            $imgname = $domainname.'logo.png';
            return new moodle_url("/theme/qubitsalpha/pix/logo/$imgname");
        }
        $logo = get_config('core_admin', 'logo');
        if (empty($logo)) {
            return false;
        }

        // 200px high is the default image size which should be displayed at 100px in the page to account for retina displays.
        // It's not worth the overhead of detecting and serving 2 different images based on the device.

        // Hide the requested size in the file path.
        $filepath = ((int) $maxwidth . 'x' . (int) $maxheight) . '/';

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return moodle_url::make_pluginfile_url(context_system::instance()->id, 'core_admin', 'logo', $filepath,
            theme_get_revision(), $logo);
    }

    /**
     * Return the site's compact logo URL, if any.
     *
     * @param int $maxwidth The maximum width, or null when the maximum width does not matter.
     * @param int $maxheight The maximum height, or null when the maximum height does not matter.
     * @return moodle_url|false
     */
    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        global $CFG;
        if((SITE_MAIN_DOMAIN != $_SERVER['HTTP_HOST'])){
            $domainname = str_replace(".", "", $_SERVER['HTTP_HOST']);
            $imgname = $domainname.'logosmall.png';
            return new moodle_url("/theme/qubitsalpha/pix/logo/$imgname");
        }
        $logo = get_config('core_admin', 'logocompact');
        if (empty($logo)) {
            return false;
        }

        // Hide the requested size in the file path.
        $filepath = ((int) $maxwidth . 'x' . (int) $maxheight) . '/';

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return moodle_url::make_pluginfile_url(context_system::instance()->id, 'core_admin', 'logocompact', $filepath,
            theme_get_revision(), $logo);
    }

}