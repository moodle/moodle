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
 * Config advertising setting.  Display only no storage.
 *
 * @package    theme
 * @subpackage essential
 * @copyright  &copy; 2017-onwards G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class essential_admin_setting_advertising extends admin_setting_heading {

    protected $linkurl;
    protected $imageurl;
    protected $imagealttext;

    /**
     * not a setting, just text
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $heading heading
     * @param string $tagline Advert tag line.
     * @param string $linkurl URL to separate advertisment page - optional.
     * @param string $imageurl URL to advert image - optional.
     * @param string $imagealttext advert image alternative text - optional.
     */
    public function __construct($name, $heading, $tagline, $linkurl = false, $imageurl = false, $imagealttext = '') {
        $this->nosave = true;
        $this->linkurl = $linkurl;
        $this->imageurl = $imageurl;
        $this->imagealttext = $imagealttext;
        parent::__construct($name, $heading, $tagline, '');
    }

    /**
     * Returns an HTML string
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;
        $context = new stdClass();
        $context->title = $this->visiblename;
        $context->description = (!empty($this->description));
        $context->descriptionformatted = highlight($query, markdown_to_html($this->description));
        $context->linkurl = $this->linkurl;
        $context->advertimage = $this->imageurl;
        $context->advertalttext = $this->imagealttext;
        return $OUTPUT->render_from_template('theme_essential/admin_setting_advertising', $context);
    }
}
