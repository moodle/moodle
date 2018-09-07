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
 * Get properties setting.
 *
 * @package    theme
 * @subpackage essential
 * @copyright  &copy; 2017-onwards G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

class essential_admin_setting_getprops extends admin_setting {

    private $props;
    private $returnbuttonname;
    private $settingsectionname;
    private $saveprops;
    private $savepropsbuttonname;

    /**
     * Not a setting, just properties.
     * @param string $name Unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $heading Heading.
     * @param string $information Text in box.
     */
    public function __construct($name, $heading, $information, $props, $settingsectionname, $returnbuttonname, $savepropsbuttonname, $saveprops) {
        $this->nosave = true;
        $this->props = $props;
        $this->returnbuttonname = $returnbuttonname;
        $this->settingsectionname = $settingsectionname;
        $this->savepropsbuttonname = $savepropsbuttonname;
        $this->saveprops = $saveprops;
        parent::__construct($name, $heading, $information, ''); // Last parameter is default.
    }

    public function get_setting() {
        return '';
    }

    public function get_defaultsetting() {
        return '';
    }

    /**
     * Never write settings
     * @return string Always returns an empty string
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Returns an HTML string
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        $return = '';

        if ($this->saveprops) {
            $returnurl = new moodle_url('/admin/settings.php', array('section' => $this->settingsectionname));
            $returnbutton = '<div class="singlebutton"><a class="btn btn-default" href="'.$returnurl->out(true).'">'.
                $this->returnbuttonname.'</a></div>';
            $return .= $returnbutton;
            $return .= '<div class="well" style="word-break: break-all;">';
            $return .= json_encode($this->props, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
            $return .= '</div>';
            $return .= $returnbutton;
        } else {
            $propsexporturl = new moodle_url('/admin/settings.php', array('section' => $this->settingsectionname,
                $this->name.'_saveprops' => 1));

            $propsexportbutton = '<div class="singlebutton"><div><a class="btn btn-default" href="'.$propsexporturl->out(true).'">'.
                $this->savepropsbuttonname.'</a></div></div>';
            $table = new html_table();
            $table->head = array($this->visiblename, markdown_to_html($this->description));
            $table->colclasses = array('leftalign', 'leftalign');
            $table->id = 'adminprops_'.$this->name;
            $table->attributes['class'] = 'admintable generaltable';
            $table->data  = array();

            foreach ($this->props as $propname => $propvalue) {
                $table->data[] = array($propname, $propvalue);
            }
            $return .= $propsexportbutton;
            $return .= html_writer::table($table);
            $return .= $propsexportbutton;
        }

        return $return;
    }
}