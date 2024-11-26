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
 * @package    theme_adaptable
 * @copyright  2018 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

use context_system;
use context_user;
use core_table\output\html_table;
use core\output\html_writer;
use core\url;

/**
 * Get properties class.
 */
class admin_setting_getprops extends \admin_setting {
    /** @var string Plugin frankenstyle. */
    private $pluginfrankenstyle;

    /** @var string Return button name. */
    private $returnbuttonname;

    /** @var string Section name. */
    private $settingsectionname;

    /** @var string Save properties button name. */
    private $savepropsbuttonname;

    /** @var string Save properties with files as a string button name. */
    private $savepropsfilestoobuttonname;

    /** @var string Save properties with files as a file button name. */
    private $savepropsfilestoofilebuttonname;

    /**
     * Not a setting, just properties.
     * @param string $name Unique ascii name, either 'mysetting' for settings that in config,
     * or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $heading Heading.
     * @param string $information Text in box.
     * @param string $pluginfrankenstyle Plugin frankenstyle.
     * @param string $props Properties.
     * @param string $settingsectionname Setting section name.
     * @param string $returnbuttonname Return button name.
     * @param string $savepropsbuttonname Save properties button name.
     * @param string $savepropsfilestoobuttonname Save properties with files as a string button name.
     * @param string $savepropsfilestoofilebuttonname Save properties with files as a file button name.
     */
    public function __construct(
        $name,
        $heading,
        $information,
        $pluginfrankenstyle,
        $settingsectionname,
        $returnbuttonname,
        $savepropsbuttonname,
        $savepropsfilestoobuttonname,
        $savepropsfilestoofilebuttonname
    ) {
        $this->nosave = true;
        $this->pluginfrankenstyle = $pluginfrankenstyle;
        $this->returnbuttonname = $returnbuttonname;
        $this->settingsectionname = $settingsectionname;
        $this->savepropsbuttonname = $savepropsbuttonname;
        $this->savepropsfilestoobuttonname = $savepropsfilestoobuttonname;
        $this->savepropsfilestoofilebuttonname = $savepropsfilestoofilebuttonname;
        parent::__construct($name, $heading, $information, ''); // Last parameter is default.
    }

    /**
     * Get setting method.
     * @return none
     */
    public function get_setting() {
        return '';
    }

    /**
     * Get default settings method.
     * @return string ''
     */
    public function get_defaultsetting() {
        return '';
    }

    /**
     * Never write settings
     *
     * @param string $data setting to write
     *
     * @return string Always returns an empty string
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Returns an HTML string
     *
     * @param string $data Data
     * @param string $query Query
     *
     * @return string Returns an HTML string
     */
    public function output_html($data, $query = '') {
        $return = '';

        $saveprops = optional_param($this->pluginfrankenstyle.'_getprops_saveprops', 0, PARAM_INT);
        $savepropsfilestoo = optional_param($this->pluginfrankenstyle.'_getprops_saveprops_filestoo', 0, PARAM_INT);
        $savepropsfilestoofile = optional_param($this->pluginfrankenstyle.'_getprops_saveprops_filestoofile', 0, PARAM_INT);
        if ($saveprops) {
            $props = \theme_adaptable\toolbox::get_properties($this->pluginfrankenstyle);

            $returnurl = new url('/admin/settings.php', ['section' => $this->settingsectionname]);
            $returnbutton = '<div class="singlebutton"><a class="btn btn-secondary" href="' . $returnurl->out(true) . '">' .
                $this->returnbuttonname . '</a></div>';
            $return .= $returnbutton;
            $return .= '<hr>';
            $return .= '<div class="alert alert-success word-break-all" role="alert">';
            $return .= json_encode($props[\theme_adaptable\toolbox::PROPS],
                JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
            $return .= '</div>';
            $return .= $returnbutton;
            $return .= '<hr>';
        } else if (($savepropsfilestoo) || ($savepropsfilestoofile)) {
            $props = \theme_adaptable\toolbox::get_properties($this->pluginfrankenstyle, true);

            $jsonprops = json_encode($props[\theme_adaptable\toolbox::PROPS],
                JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);

            $alertstate = 'success';
            if ($savepropsfilestoofile) {
                $fs = get_file_storage();
                $syscontext = context_system::instance();
                $files = $fs->get_area_files(
                    $syscontext->id, $this->pluginfrankenstyle, 'propertyfiles', 0, 'filepath,filename', false);
                if (count($files) < 8) {
                    global $USER;
                    $time = time();
                    $datetime = new \DateTime("now", \core_date::get_user_timezone_object());
                    // Appended seconds.
                    $userdate = userdate($datetime->getTimestamp(), get_string('backupnameformat', 'core_langconfig').'%S');
                    $filename = "Adaptable_".get_string('settings')."_".$userdate.".json";

                    $filerecord = [
                        'contextid' => context_user::instance($USER->id)->id,
                        'component' => 'user',
                        'filearea' => 'draft',
                        'itemid' => file_get_unused_draft_itemid(),
                        'filepath' => '/',
                        'filename' => $filename,
                        // Don't use userid as could be different!
                        'author' => fullname($USER, true),
                        'license' => '',
                        'timecreated' => $time,
                        'timemodified' => $time,
                        'mimetype' => 'application/json',
                    ];
                    $draftjson = $fs->create_file_from_string($filerecord, $jsonprops); // Draft.

                    // Able to make file....
                    $filerecord = [
                        'contextid' => $syscontext->id,
                        'component' => $this->pluginfrankenstyle,
                        'filearea' => 'propertyfiles',
                        'itemid' => '0',
                        'filepath' => $draftjson->get_filepath(),
                        'filename' => $draftjson->get_filename(),
                        'author' => $draftjson->get_author(),
                        'license' => $draftjson->get_license(),
                        'timecreated' => $draftjson->get_timecreated(),
                        'timemodified' => $draftjson->get_timemodified(),
                        'mimetype' => $draftjson->get_mimetype(),
                    ];
                    $settingfile = $fs->create_file_from_storedfile($filerecord, $draftjson); // Replacement.

                    $draftjson->delete(); // Finished with draft.

                    $savepropsfilestoofileresult = get_string('propertiesexportfilestoofilesuccess', $this->pluginfrankenstyle, $settingfile->get_filename());
                } else {
                    $savepropsfilestoofileresult = get_string('propertiesexportfilestoofilefail', $this->pluginfrankenstyle);
                    $alertstate = 'warning';
                }
            }

            $returnurl = new url('/admin/settings.php', ['section' => $this->settingsectionname]);
            $returnbutton = '<div class="singlebutton"><a class="btn btn-secondary" href="' . $returnurl->out(true) . '">' .
                $this->returnbuttonname . '</a></div>';
            $return .= $returnbutton;
            $return .= '<hr>';
            $return .= '<div class="alert alert-'.$alertstate.' word-break-all" role="alert">';
            if ($savepropsfilestoofile) {
                $return .= $savepropsfilestoofileresult;
            } else {
                $return .= $jsonprops;
            }
            $return .= '</div>';
            $return .= $returnbutton;
            $return .= '<hr>';

        } else {
            $props = \theme_adaptable\toolbox::get_properties($this->pluginfrankenstyle);

            $propsexporturl = new url('/admin/settings.php', ['section' => $this->settingsectionname,
                $this->pluginfrankenstyle . '_getprops_saveprops' => 1, ]);
            $propsexportbutton = '<div class="singlebutton"><div><a class="btn btn-secondary" href="' .
                $propsexporturl->out(true) . '" data-toggle="tooltip" data-placement="bottom" title="'.
                get_string('propertiesexporthelp', $this->pluginfrankenstyle) . '">' .
                $this->savepropsbuttonname . '</a></div></div>';

            $propsexportfilestoourl = new url('/admin/settings.php', ['section' => $this->settingsectionname,
                $this->pluginfrankenstyle . '_getprops_saveprops_filestoo' => 1, ]);
            $propsexportfilestoobutton = '<div class="singlebutton"><div><a class="btn btn-secondary" href="' .
                $propsexportfilestoourl->out(true) . '" data-toggle="tooltip" data-placement="bottom" title="'.
                get_string('propertiesexportfilestoohelp', $this->pluginfrankenstyle) . '">' .
                $this->savepropsfilestoobuttonname . '</a></div></div>';

            $propsexportfilestoofilesurl = new url('/admin/settings.php', ['section' => $this->settingsectionname,
                $this->pluginfrankenstyle . '_getprops_saveprops_filestoofile' => 1, ]);
            $propsexportfilestoofilebutton = '<div class="singlebutton"><div><a class="btn btn-secondary" href="' .
                $propsexportfilestoofilesurl->out(true) . '" data-toggle="tooltip" data-placement="bottom" title="'.
                get_string('propertiesexportfilestoofilehelp', $this->pluginfrankenstyle) . '">' .
                $this->savepropsfilestoofilebuttonname . '</a></div></div>';

                $table = new html_table();
            $table->head = [$this->visiblename, markdown_to_html($this->description)];
            $table->colclasses = ['leftalign', 'leftalign'];
            $table->id = 'adminprops_' . $this->name;
            $table->attributes['class'] = 'admintable generaltable';
            $table->data = [];

            foreach ($props[\theme_adaptable\toolbox::PROPS] as $propname => $propvalue) {
                $table->data[] = [$propname, '<pre>' . htmlentities($propvalue, ENT_COMPAT) . '</pre>'];
            }
            $return .= $propsexportbutton;
            $return .= $propsexportfilestoobutton;
            $return .= $propsexportfilestoofilebutton;
            $return .= '<hr>';
            $return .= html_writer::table($table);
            $return .= $propsexportbutton;
            $return .= $propsexportfilestoobutton;
            $return .= $propsexportfilestoofilebutton;
            $return .= '<hr>';
        }

        return $return;
    }
}
