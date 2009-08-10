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
 * Interface and classes for icon finders.
 *
 * Please see http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 * for an overview.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * An icon finder is responsible for working out the correct URL for an icon.
 *
 * A icon finder must also have a constructor that takes a theme object.
 * (See {@link standard_icon_finder::__construct} for an example.)
 *
 * Note that we are planning to change the Moodle icon naming convention before
 * the Moodle 2.0 release. Therefore, this API will probably change.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
interface icon_finder {
    /**
     * Return the URL for an icon identified as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->pixpath/i/course.gif";
     * then old_icon_url('i/course'); will return the equivalent URL that is correct now.
     *
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname);

    /**
     * Return the URL for an icon identified as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->modpixpath/$mod/icon.gif";
     * then mod_icon_url('icon', $mod); will return the equivalent URL that is correct now.
     *
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module);
}

/**
 * This icon finder implements the old scheme that was used when themes that had
 * $THEME->custompix = false.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class pix_icon_finder implements icon_finder {
    /**
     * Constructor
     * @param theme_config $theme the theme we are finding icons for (which is irrelevant).
     */
    public function __construct($theme) {
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        global $CFG;
        if (file_exists($CFG->dirroot . '/pix/' . $iconname . '.png')) {
            return $CFG->httpswwwroot . '/pix/' . $iconname . '.png';
        } else {
            return $CFG->httpswwwroot . '/pix/' . $iconname . '.gif';
        }
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module) {
        global $CFG;
        if (file_exists($CFG->dirroot . '/mod/' . $module . '/' . $iconname . '.png')) {
            return $CFG->httpswwwroot . '/mod/' . $module . '/' . $iconname . '.png';
        } else {
            return $CFG->httpswwwroot . '/mod/' . $module . '/' . $iconname . '.gif';
        }
    }
}


/**
 * This icon finder implements the old scheme that was used for themes that had
 * $THEME->custompix = true.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class theme_icon_finder implements icon_finder {
    protected $themename;
    /**
     * Constructor
     * @param theme_config $theme the theme we are finding icons for.
     */
    public function __construct($theme) {
        $this->themename = $theme->name;
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        global $CFG;
        if (file_exists($CFG->themedir . '/' . $this->themename . '/pix/' . $iconname . '.png')) {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/' . $iconname . '.png';
        } else {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/' . $iconname . '.gif';
        }
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module) {
        global $CFG;
        if (file_exists($CFG->themedir . '/' . $this->themename . '/pix/mod/' . $module . '/' . $iconname . '.png')) {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/mod/' . $module . '/' . $iconname . '.png';
        } else {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/mod/' . $module . '/' . $iconname . '.gif';
        }
    }
}


/**
 * This icon finder implements the algorithm in pix/smartpix.php.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class smartpix_icon_finder extends pix_icon_finder {
    protected $places = array();

    /**
     * Constructor
     * @param theme_config $theme the theme we are finding icons for.
     */
    public function __construct($theme) {
        global $CFG;
        $this->places[$CFG->themedir . '/' . $theme->name . '/pix/'] =
                $CFG->httpsthemewww . '/' . $theme->name . '/pix/';
        if (!empty($theme->parent)) {
            $this->places[$CFG->themedir . '/' . $theme->parent . '/pix/'] =
                    $CFG->httpsthemewww . '/' . $theme->parent . '/pix/';
        }
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        foreach ($this->places as $dirroot => $urlroot) {
            if (file_exists($dirroot . $iconname . '.png')) {
                return $dirroot . $iconname . '.png';
            } else if (file_exists($dirroot . $iconname . '.gif')) {
                return $dirroot . $iconname . '.gif';
            }
        }
        return parent::old_icon_url($iconname);
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module) {
        foreach ($this->places as $dirroot => $urlroot) {
            if (file_exists($dirroot . 'mod/' . $iconname . '.png')) {
                return $dirroot . 'mod/' . $iconname . '.png';
            } else if (file_exists($dirroot . 'mod/' . $iconname . '.gif')) {
                return $dirroot . 'mod/' . $iconname . '.gif';
            }
        }
        return parent::old_icon_url($iconname, $module);
    }
}


