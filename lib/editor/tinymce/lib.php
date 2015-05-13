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
 * TinyMCE text editor integration.
 *
 * @package    editor
 * @subpackage tinymce
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class tinymce_texteditor extends texteditor {
    /** @var string active version - this is the directory name where to find tinymce code */
    public $version = '3.5.11';

    /**
     * Is the current browser supported by this editor?
     * @return bool
     */
    public function supported_by_browser() {
        // We don't support any browsers which it doesn't support.
        return true;
    }

    /**
     * Returns array of supported text formats.
     * @return array
     */
    public function get_supported_formats() {
        // FORMAT_MOODLE is not supported here, sorry.
        return array(FORMAT_HTML => FORMAT_HTML);
    }

    /**
     * Returns text format preferred by this editor.
     * @return int
     */
    public function get_preferred_format() {
        return FORMAT_HTML;
    }

    /**
     * Does this editor support picking from repositories?
     * @return bool
     */
    public function supports_repositories() {
        return true;
    }

    /**
     * Sets up head code if necessary.
     */
    public function head_setup() {
    }

    /**
     * Use this editor for give element.
     *
     * @param string $elementid
     * @param array $options
     * @param null $fpoptions
     */
    public function use_editor($elementid, array $options=null, $fpoptions=null) {
        global $PAGE, $CFG;
        // Note: use full moodle_url instance to prevent standard JS loader, make sure we are using https on profile page if required.
        if ($CFG->debugdeveloper) {
            $PAGE->requires->js(new moodle_url($CFG->httpswwwroot.'/lib/editor/tinymce/tiny_mce/'.$this->version.'/tiny_mce_src.js'));
        } else {
            $PAGE->requires->js(new moodle_url($CFG->httpswwwroot.'/lib/editor/tinymce/tiny_mce/'.$this->version.'/tiny_mce.js'));
        }
        $PAGE->requires->js_init_call('M.editor_tinymce.init_editor', array($elementid, $this->get_init_params($elementid, $options)), true);
        if ($fpoptions) {
            $PAGE->requires->js_init_call('M.editor_tinymce.init_filepicker', array($elementid, $fpoptions), true);
        }
    }

    protected function get_init_params($elementid, array $options=null) {
        global $CFG, $PAGE, $OUTPUT;

        //TODO: we need to implement user preferences that affect the editor setup too

        $directionality = get_string('thisdirection', 'langconfig');
        $strtime        = get_string('strftimetime');
        $strdate        = get_string('strftimedaydate');
        $lang           = current_language();
        $contentcss     = $PAGE->theme->editor_css_url()->out(false);

        $context = empty($options['context']) ? context_system::instance() : $options['context'];

        $config = get_config('editor_tinymce');
        if (!isset($config->disabledsubplugins)) {
            $config->disabledsubplugins = '';
        }

        $fontselectlist = empty($config->fontselectlist) ? '' : $config->fontselectlist;

        $langrev = -1;
        if (!empty($CFG->cachejs)) {
            $langrev = get_string_manager()->get_revision();
        }

        $params = array(
            'moodle_config' => $config,
            'mode' => "exact",
            'elements' => $elementid,
            'relative_urls' => false,
            'document_base_url' => $CFG->httpswwwroot,
            'moodle_plugin_base' => "$CFG->httpswwwroot/lib/editor/tinymce/plugins/",
            'content_css' => $contentcss,
            'language' => $lang,
            'directionality' => $directionality,
            'plugin_insertdate_dateFormat ' => $strdate,
            'plugin_insertdate_timeFormat ' => $strtime,
            'theme' => "advanced",
            'skin' => "moodle",
            'apply_source_formatting' => true,
            'remove_script_host' => false,
            'entity_encoding' => "raw",
            'plugins' => 'lists,table,style,layer,advhr,advlink,emotions,inlinepopups,' .
                'searchreplace,paste,directionality,fullscreen,nonbreaking,contextmenu,' .
                'insertdatetime,save,iespell,preview,print,noneditable,visualchars,' .
                'xhtmlxtras,template,pagebreak',
            'gecko_spellcheck' => true,
            'theme_advanced_font_sizes' => "1,2,3,4,5,6,7",
            'theme_advanced_layout_manager' => "SimpleLayout",
            'theme_advanced_toolbar_align' => "left",
            'theme_advanced_fonts' => $fontselectlist,
            'theme_advanced_resize_horizontal' => true,
            'theme_advanced_resizing' => true,
            'theme_advanced_resizing_min_height' => 30,
            'min_height' => 30,
            'theme_advanced_toolbar_location' => "top",
            'theme_advanced_statusbar_location' => "bottom",
            'language_load' => false, // We load all lang strings directly from Moodle.
            'langrev' => $langrev,
        );

        // Should we override the default toolbar layout unconditionally?
        if (!empty($config->customtoolbar) and $customtoolbar = self::parse_toolbar_setting($config->customtoolbar)) {
            $i = 1;
            foreach ($customtoolbar as $line) {
                $params['theme_advanced_buttons'.$i] = $line;
                $i++;
            }
        } else {
            // At least one line is required.
            $params['theme_advanced_buttons1'] = '';
        }

        if (!empty($config->customconfig)) {
            $config->customconfig = trim($config->customconfig);
            $decoded = json_decode($config->customconfig, true);
            if (is_array($decoded)) {
                foreach ($decoded as $k=>$v) {
                    $params[$k] = $v;
                }
            }
        }

        if (!empty($options['legacy']) or !empty($options['noclean']) or !empty($options['trusted'])) {
            // now deal somehow with non-standard tags, people scream when we do not make moodle code xtml strict,
            // but they scream even more when we strip all tags that are not strict :-(
            $params['valid_elements'] = 'script[src|type],*[*]'; // for some reason the *[*] does not inlcude javascript src attribute MDL-25836
            $params['invalid_elements'] = '';
        }
        // Add unique moodle elements - unfortunately we have to decide if these are SPANs or DIVs.
        $params['extended_valid_elements'] = 'nolink,tex,algebra,lang[lang]';
        $params['custom_elements'] = 'nolink,~tex,~algebra,lang';

        //Add onblur event for client side text validation
        if (!empty($options['required'])) {
            $params['init_instance_callback'] = 'M.editor_tinymce.onblur_event';
        }

        // Allow plugins to adjust parameters.
        editor_tinymce_plugin::all_update_init_params($params, $context, $options);

        // Remove temporary parameters.
        unset($params['moodle_config']);

        return $params;
    }

    /**
     * Parse the custom toolbar setting.
     * @param string $customtoolbar
     * @return array csv toolbar lines
     */
    public static function parse_toolbar_setting($customtoolbar) {
        $result = array();
        $customtoolbar = trim($customtoolbar);
        if ($customtoolbar === '') {
            return $result;
        }
        $customtoolbar = str_replace("\r", "\n", $customtoolbar);
        $customtoolbar = strtolower($customtoolbar);
        $i = 0;
        foreach (explode("\n", $customtoolbar) as $line) {
            $line = preg_replace('/[^a-z0-9_,\|\-]/', ',', $line);
            $line = str_replace('|', ',|,', $line);
            $line = preg_replace('/,,+/', ',', $line);
            $line = trim($line, ',|');
            if ($line === '') {
                continue;
            }
            if ($i == 10) {
                // Maximum is ten lines, merge the rest to the last line.
                $result[9] = $result[9].','.$line;
            } else {
                $result[] = $line;
                $i++;
            }
        }
        return $result;
    }

    /**
     * Gets a named plugin object. Will cause fatal error if plugin doesn't
     * exist. This is intended for use by plugin files themselves.
     *
     * @param string $plugin Name of plugin e.g. 'moodleemoticon'
     * @return editor_tinymce_plugin Plugin object
     */
    public function get_plugin($plugin) {
        global $CFG;
        return editor_tinymce_plugin::get($plugin);
    }

    /**
     * Equivalent to tinyMCE.baseURL value available from JavaScript,
     * always use instead of /../ when referencing tinymce core code from moodle plugins!
     *
     * @return moodle_url url pointing to the root of TinyMCE javascript code.
     */
    public function get_tinymce_base_url() {
        global $CFG;
        return new moodle_url("$CFG->httpswwwroot/lib/editor/tinymce/tiny_mce/$this->version/");
    }

}
