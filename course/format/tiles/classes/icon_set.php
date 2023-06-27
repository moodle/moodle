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
 * Icon set class for format tiles.
 * @package    format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_tiles;

defined('MOODLE_INTERNAL') || die();

/**
 * Icon set class for format tiles.
 * @package    format_tiles
 * @copyright  2019 David Watson {@link http://evolutioncode.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class icon_set {

    /**
     * These are shown on tiles.  The teacher chooses which one to apply to each tile or course.
     * To make more icons available to a teacher, add them here using the set at https://fontawesome.com/v4.7.0/icons/.
     * Alternatively, if your theme does not support Font Awesome or you want to use image files, add them to pix/tileicon.
     * The files in pix/tileicon are only used as a fallback, if the theme does not support font awesome.
     * In both cases, you will then need to purge all caches to ensure that the new icon is visible.
     * @var array
     */
    private $fontawesometileicons = [
        'fa-asterisk',
        'fa-address-book-o',
        'fa-balance-scale',
        'fa-bar-chart',
        'fa-bell-o',
        'fa-binoculars',
        'fa-bitcoin',
        'fa-bookmark-o',
        'fa-briefcase',
        'fa-building',
        'fa-bullhorn',
        'fa-bullseye',
        'fa-calculator',
        'fa-calendar',
        'fa-calendar-check-o',
        'fa-check',
        'fa-child',
        'fa-clock-o',
        'fa-clone',
        'fa-cloud-download',
        'fa-cloud-upload',
        'fa-comment-o',
        'fa-comments-o',
        'fa-compass',
        'fa-diamond',
        'fa-dollar',
        'fa-euro',
        'fa-exclamation-triangle',
        'fa-feed',
        'fa-file-text-o',
        'fa-film',
        'fa-flag-checkered',
        'fa-flag-o',
        'fa-flash',
        'fa-flask',
        'fa-frown-o',
        'fa-gavel',
        'fa-gbp',
        'fa-globe',
        'fa-handshake-o',
        'fa-headphones',
        'fa-heartbeat',
        'fa-history',
        'fa-home',
        'fa-id-card-o',
        'fa-info',
        'fa-key',
        'fa-laptop',
        'fa-life-buoy',
        'fa-lightbulb-o',
        'fa-line-chart',
        'fa-list',
        'fa-list-ol',
        'fa-location-arrow',
        'fa-map-marker',
        'fa-map-o',
        'fa-map-signs',
        'fa-microphone',
        'fa-mobile-phone',
        'fa-mortar-board',
        'fa-music',
        'fa-newspaper-o',
        'fa-pencil-square-o',
        'fa-pie-chart',
        'fa-podcast',
        'fa-puzzle-piece',
        'fa-question-circle',
        'fa-random',
        'fa-refresh',
        'fa-road',
        'fa-search',
        'fa-sliders',
        'fa-smile-o',
        'fa-star',
        'fa-star-half-o',
        'fa-star-o',
        'fa-tags',
        'fa-tasks',
        'fa-television',
        'fa-thumbs-o-down',
        'fa-thumbs-o-up',
        'fa-trophy',
        'fa-umbrella',
        'fa-university',
        'fa-user-o',
        'fa-users',
        'fa-volume-up',
        'fa-wrench'
    ];

    /**
     * In order to populate the option menus under course setting which allow the user to select
     * a tile icon from all those available, iterates through all font awesome icons and images in
     * the relevant directory and generates a suitable menu option for each icon.
     * As to the display name, for an icon in the pix directory (e.g. book.svg) then lang string 'icontitle-book" is sought.
     * Likewise for a font awesome icon called 'fa-tasks', a lang string 'icontitle-tasks' is sought.
     * If the language string is not found (e.g. it is a custom icon added to pix with no lang string), filename is used.
     * @param int $courseid the id of the course we are in (so we can mark the default icon for this course).
     * @return array of tile icons
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function available_tile_icons($courseid = 0) {
        global $CFG, $DB;
        $stringmanager = get_string_manager();
        $availableicons = [];

        // First if the theme supports font awesome, use the available font awesome tile icons.
        if ($this->supports_font_awesome()) {
            foreach ($this->fontawesometileicons as $iconname) {
                $pixname = str_replace('fa-', '', $iconname);
                if ($stringmanager->string_exists('icontitle-' . $pixname, 'format_tiles')) {
                    $displayname = get_string('icontitle-' . $pixname, 'format_tiles');
                } else {
                    $displayname = ucwords(str_replace('_', ' ', (str_replace('-', ' ', $pixname))));
                }
                $availableicons[$pixname] = $displayname;
            }
        }

        // Now look for any supplemental image file (i.e. non font awesome icons) which are available as tile icons.
        // Add them to the list.
        // Ideally all of the icons would be removed from the pix directorym except custom non font-awesome icons.
        // The reason is that those fallback images are never called if the theme is font awesome compatible (as fa is used).
        // However they are left in pix for now as some older themes (e.g. Clean) may need them.
        $iconsindirectory = get_directory_list($CFG->dirroot
            . '/course/format/tiles/pix/tileicon', '', false, false, true);
        foreach ($iconsindirectory as $icon) {
            $filename = explode('.', $icon)[0];
            // If we don't already have it from font awesome (e.g. book, flipchart, assessment_timer), then add it here.
            if (!isset($availableicons[$filename])) {
                if ($stringmanager->string_exists('icontitle-' . $filename, 'format_tiles')) {
                    $displayname = get_string('icontitle-' . $filename, 'format_tiles');
                } else {
                    $displayname = ucwords(str_replace('_', ' ', (str_replace('-', ' ', $filename))));
                }
                $availableicons[$filename] = $displayname;
            }
        }
        asort($availableicons);

        if (!$courseid) {
            return $availableicons;
        } else {
            // Put the default course icon in first place.
            $defaulticon = $DB->get_field('course_format_options', 'value', array(
                'courseid' => $courseid,
                'format' => 'tiles',
                'sectionid' => 0,
                'name' => 'defaulttileicon'
            ));
            if ($defaulticon) {
                $removedicondescription = $availableicons[$defaulticon] . ' (' . get_string('default') . ')';
                unset($availableicons[$defaulticon]);
                $availableicons = array_merge(array($defaulticon => $removedicondescription), $availableicons);
            }
            return $availableicons;
        }
    }

    /**
     * Does the theme support Font Awesome or not?
     * By setting this to return false, developers can disable FA for this plugin if they need to.
     * Sometimes a theme *seems* to support FA, but in reality does so with issues.
     * If set to false, images in pix/tileicon etc will be used instead of FA.
     * If you change this in code, purge all caches afterwards.
     * @return bool
     */
    public function supports_font_awesome() {
        global $PAGE;
        if (!class_exists('\core\output\icon_system') || !method_exists($PAGE->theme, 'get_icon_system')) {
            return false;
        }

        $fontawesomethemeswhitelist = ['moove'];
        // Using $PAGE->theme->get_icon_system()==icons_system::fontawesome does not work for Moove.
        // However Moove does support font awesome for {{pix}}, so we add a whitelist too.
        if (array_search($PAGE->theme->name, $fontawesomethemeswhitelist) !== false) {
            return true;
        }
        try {
            return $PAGE->theme->get_icon_system() == \core\output\icon_system::FONTAWESOME;
        } catch (\Exception $ex) {
            debugging(
                'Could not get theme icon system. Using fallback /pix images for tile icons. ' . $ex->getMessage(),
                DEBUG_DEVELOPER
            );
        }
        return false;
    }

    /**
     * Lib.php calls this for example when caches are purged or plugin is updated, to get latest FA icon map.
     * @see format_tiles_get_fontawesome_icon_map()
     * @return array
     */
    public function get_font_awesome_icon_map() {
        if (!$this->supports_font_awesome()) {
            return [];
        }
        // First the general icons (not specific to tiles).
        // These are used for example to show nav buttons within tiles.
        $generalicons = [
            'format_tiles:camera' => 'fa-camera',
            'format_tiles:check' => 'fa-check',
            'format_tiles:chevron-left' => 'fa-chevron-left',
            'format_tiles:chevron-right' => 'fa-chevron-right',
            'format_tiles:clone' => 'fa-clone',
            'format_tiles:close' => 'fa-close',
            'format_tiles:cloud-download' => 'fa-cloud-download',
            'format_tiles:cloud-upload' => 'fa-cloud-upload',
            'format_tiles:filter' => 'fa-filter',
            'format_tiles:eye-slash' => 'fa-eye-slash',
            'format_tiles:home' => 'fa-home',
            'format_tiles:lock' => 'fa-lock',
            'format_tiles:star-o' => 'fa-star-o',
            'format_tiles:pencil' => 'fa-pencil',
            'format_tiles:random' => 'fa-random',
            'format_tiles:star' => 'fa-star',
            'format_tiles:toggle-off' => 'fa-toggle-off',
            'format_tiles:toggle-on' => 'fa-toggle-on'
        ];

         // These are used on sub-tiles (if used) e.g. to show PDF, Excel activities.
        $subtileicons = [
            'format_tiles:subtile/comments-o' => 'fa-comments-o',
            'format_tiles:subtile/database' => 'fa-database',
            'format_tiles:subtile/feedback' => 'fa-bullhorn',
            'format_tiles:subtile/file-excel' => 'fa-table',
            'format_tiles:subtile/file-pdf-o' => 'fa-file-pdf-o',
            'format_tiles:subtile/file-powerpoint-o' => 'fa-file-powerpoint-o',
            'format_tiles:subtile/file-text-o' => 'fa-file-text-o',
            'format_tiles:subtile/file-word-o' => 'fa-file-word-o',
            'format_tiles:subtile/file-zip-o' => 'fa-file-zip-o',
            'format_tiles:subtile/film' => 'fa-film',
            'format_tiles:subtile/folder-o' => 'fa-folder-o',
            'format_tiles:subtile/globe' => 'fa-globe',
            'format_tiles:subtile/puzzle-piece' => 'fa-puzzle-piece',
            'format_tiles:subtile/question-circle' => 'fa-question-circle',
            'format_tiles:subtile/star' => 'fa-star',
            'format_tiles:subtile/star-o' => 'fa-star-o',
            'format_tiles:subtile/survey' => 'fa-bar-chart',
            'format_tiles:subtile/volume-up' => 'fa-volume-up'
        ];

        $tileicons = [];
        foreach ($this->fontawesometileicons as $icon) {
            $pixname = str_replace('fa-', '', $icon);
            $tileicons['format_tiles:tileicon/' . $pixname] = $icon;
        }
        return array_merge($tileicons, $generalicons, $subtileicons);
    }
}
