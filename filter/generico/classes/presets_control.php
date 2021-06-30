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
 * @package    filter
 * @subpackage generico
 * @copyright  2014 Justin Hunt <poodllsupport@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace filter_generico;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/adminlib.php');

class presets_control extends \admin_setting {

    /** @var mixed int index of template */
    public $templateindex;
    /** @var array template data for spec index */
    public $presetdata;
    public $visiblename;
    public $information;

    /**
     * not a setting, just text
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in
     *         config_plugins.
     * @param string $heading heading
     * @param string $information text in box
     */
    public function __construct($name, $visiblename, $information, $templateindex, $presetdata=false) {
        $this->nosave = true;
        $this->templateindex = $templateindex;
        if(!$presetdata){
            $presetdata=$this->fetch_presets();
        }
        $this->presetdata = $presetdata;
        $this->visiblename = $visiblename;
        $this->information = $information;
        parent::__construct($name, $visiblename, $information, $templateindex);
    }

    /**
     * Always returns true
     *
     * @return bool Always returns true
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true
     *
     * @return bool Always returns true
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings
     *
     * @return string Always returns an empty string
     */
    public function write_setting($data) {
        // do not write any setting
        return '';
    }

    /**
     * Returns an HTML string
     *
     * @return string Returns an HTML string
     */
    public function output_html($data, $query = '') {
        global $PAGE;

        //build our select form
        $keys = array_keys($this->presetdata);
        $usearray = array();

        foreach ($keys as $key) {
            $name = $this->presetdata[$key]['name'];
            if (empty($name)) {
                $name = $key;
            }
            $usearray[$key] = $name;
        }

        $presetsjson = json_encode($this->presetdata);
        $presetscontrol = \html_writer::tag('input', '',
                array('id' => 'id_s_filter_generico_presetdata_' . $this->templateindex, 'type' => 'hidden',
                        'value' => $presetsjson));

        //Add javascript handler for presets
        $PAGE->requires->js_call_amd('filter_generico/generico_presets_amd',
                'init', array(array('templateindex' => $this->templateindex)));

        $select = \html_writer::select($usearray, 'filter_generico/presets', '', '--custom--');

        $dragdropsquare = \html_writer::tag('div', get_string('bundle', 'filter_generico'),
                array('id' => 'id_s_filter_generico_dragdropsquare_' . $this->templateindex,
                        'class' => 'filter_generico_dragdropsquare'));

        return format_admin_setting($this, $this->visiblename,
                $dragdropsquare . '<div class="form-text defaultsnext">' . $presetscontrol . $select . '</div>',
                $this->information, true, '', '', $query);

    }

    protected static function parse_preset_template(\SplFileInfo $fileinfo) {
        $file = $fileinfo->openFile("r");
        $content = "";
        while (!$file->eof()) {
            $content .= $file->fgets();
        }
        $preset_object = json_decode($content);
        if ($preset_object && is_object($preset_object)) {
            return get_object_vars($preset_object);
        } else {
            return false;
        }
    }//end of parse preset template

    public static function fetch_presets() {
        global $CFG, $PAGE;
        //init return array
        $ret = array();
        $dirs = array();

        //we search the Generico "presets" and the themes "generico" folders for presets
        $generico_presets_dir = $CFG->dirroot . '/filter/generico/presets';
        $theme_generico_dir = $PAGE->theme->dir . '/generico';
        if (file_exists($generico_presets_dir)) {
            $dirs[] = new \DirectoryIterator($generico_presets_dir);
        }
        if (file_exists($theme_generico_dir)) {
            $dirs[] = new \DirectoryIterator($theme_generico_dir);
        }
        foreach ($dirs as $dir) {
            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot() && !$fileinfo->isDir()) {
                    $preset = self::parse_preset_template($fileinfo);
                    if ($preset) {
                        $ret[] = $preset;
                    }
                }
            }
        }
        return $ret;
    }//end of fetch presets function

    public static function set_preset_to_config($preset, $templateindex) {
        $fields = array();
        $fields['name'] = 'templatename';
        $fields['key'] = 'templatekey';
        $fields['instructions'] = 'templateinstructions';
        $fields['body'] = 'template';
        $fields['bodyend'] = 'templateend';
        $fields['requirecss'] = 'templaterequire_css';
        $fields['requirejs'] = 'templaterequire_js';
        $fields['shim'] = 'templaterequire_js_shim';
        $fields['version'] = 'templateversion';
        $fields['defaults'] = 'templatedefaults';
        $fields['amd'] = 'template_amd';
        $fields['script'] = 'templatescript';
        $fields['style'] = 'templatestyle';
        $fields['dataset'] = 'dataset';
        $fields['datavars'] = 'datavars';

        foreach ($fields as $fieldkey => $fieldname) {
            if (array_key_exists($fieldkey, $preset)) {
                $fieldvalue = $preset[$fieldkey];
            } else {
                $fieldvalue = '';
            }
            set_config($fieldname . '_' . $templateindex, $fieldvalue, 'filter_generico');
        }
    }//End of set_preset_to_config

    public static function template_has_update($templateindex) {
        $presets = self::fetch_presets();
        foreach ($presets as $preset) {
            if (get_config('filter_generico', 'templatekey_' . $templateindex) == $preset['key']) {
                $template_version = get_config('filter_generico', 'templateversion_' . $templateindex);
                $preset_version = $preset['version'];
                if (version_compare($preset_version, $template_version) > 0) {
                    return $preset_version;
                }//end of version compare
            }//end of if keys match
        }//end of presets loop
        return false;
    }

    public static function update_all_templates() {
        $templatecount = get_config('filter_generico', 'templatecount');
        $updatecount = 0;
        for ($x = 1; $x < $templatecount + 1; $x++) {
            $updated = self::update_template($x);
            if ($updated) {
                $updatecount++;
            }
        }//end of templatecount loop
        return $updatecount;
    }//end of function

    public static function update_template($templateindex) {
        $updated = false;
        $presets = self::fetch_presets();
        foreach ($presets as $preset) {
            if (get_config('filter_generico', 'templatekey_' . $templateindex) == $preset['key']) {
                $template_version = get_config('filter_generico', 'templateversion_' . $templateindex);
                $preset_version = $preset['version'];
                if (version_compare($preset_version, $template_version) > 0) {
                    self::set_preset_to_config($preset, $templateindex);
                    $updated = true;
                }//end of version compare
                return $updated;
            }//end of if keys match
        }//end of presets loop
        return false;
    }//end of function

}