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
    /** @var string active version - directory name */
    public $version = '3.4.9';

    public function supported_by_browser() {
        if (check_browser_version('MSIE', 6)) {
            return true;
        }
        if (check_browser_version('Gecko', 20030516)) {
            return true;
        }
        if (check_browser_version('Safari', 412)) {
            return true;
        }
        if (check_browser_version('Chrome', 6)) {
            return true;
        }
        if (check_browser_version('Opera', 9)) {
            return true;
        }
        if (check_browser_version('Safari iOS', 534)) {
            return true;
        }

        return false;
    }

    public function get_supported_formats() {
        return array(FORMAT_HTML => FORMAT_HTML);
    }

    public function get_preferred_format() {
        return FORMAT_HTML;
    }

    public function supports_repositories() {
        return true;
    }

    public function head_setup() {
    }

    public function use_editor($elementid, array $options=null, $fpoptions=null) {
        global $PAGE;
        if (debugging('', DEBUG_DEVELOPER)) {
            $PAGE->requires->js('/lib/editor/tinymce/tiny_mce/'.$this->version.'/tiny_mce_src.js');
        } else {
            $PAGE->requires->js('/lib/editor/tinymce/tiny_mce/'.$this->version.'/tiny_mce.js');
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

        $context = empty($options['context']) ? get_context_instance(CONTEXT_SYSTEM) : $options['context'];

        $xmedia = 'moodlemedia,'; // HQ thinks it should be always on, so it is no matter if it will actually work or not
        /*
        if (!empty($options['legacy'])) {
            $xmedia = 'moodlemedia,';
        } else {
            if (!empty($options['noclean']) or !empty($options['trusted'])) {
            }
        }*/

        $filters = filter_get_active_in_context($context);
        if (array_key_exists('filter/tex', $filters)) {
            $xdragmath = 'dragmath,';
        } else {
            $xdragmath = '';
        }
        if (array_key_exists('filter/emoticon', $filters)) {
            $xemoticon = 'moodleemoticon,';
        } else {
            $xemoticon = '';
        }

        $params = array(
                    'mode' => "exact",
                    'elements' => $elementid,
                    'relative_urls' => false,
                    'document_base_url' => $CFG->httpswwwroot,
                    'content_css' => $contentcss,
                    'language' => $lang,
                    'directionality' => $directionality,
                    'plugin_insertdate_dateFormat ' => $strdate,
                    'plugin_insertdate_timeFormat ' => $strtime,
                    'theme' => "advanced",
                    'skin' => "o2k7",
                    'skin_variant' => "silver",
                    'apply_source_formatting' => true,
                    'remove_script_host' => false,
                    'entity_encoding' => "raw",
                    'plugins' => "{$xmedia}advimage,safari,table,style,layer,advhr,advlink,emotions,inlinepopups,searchreplace,paste,directionality,fullscreen,moodlenolink,{$xemoticon}{$xdragmath}nonbreaking,contextmenu,insertdatetime,save,iespell,preview,print,noneditable,visualchars,xhtmlxtras,template,pagebreak,spellchecker",
                    'theme_advanced_font_sizes' => "1,2,3,4,5,6,7",
                    'theme_advanced_layout_manager' => "SimpleLayout",
                    'theme_advanced_toolbar_align' => "left",
                    'theme_advanced_buttons1' => "fontselect,fontsizeselect,formatselect",
                    'theme_advanced_buttons1_add' => "|,undo,redo,|,search,replace,|,fullscreen",
                    'theme_advanced_buttons2' => "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright",
                    'theme_advanced_buttons2_add' => "|,cleanup,removeformat,pastetext,pasteword,|,forecolor,backcolor,|,ltr,rtl",
                    'theme_advanced_buttons3' => "bullist,numlist,outdent,indent,|,link,unlink,moodlenolink,|,image,{$xemoticon}{$xmedia}{$xdragmath}nonbreaking,charmap",
                    'theme_advanced_buttons3_add' => "table,|,code,spellchecker",
                    'theme_advanced_fonts' => "Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings",
                    'theme_advanced_resize_horizontal' => true,
                    'theme_advanced_resizing' => true,
                    'theme_advanced_resizing_min_height' => 30,
                    'theme_advanced_toolbar_location' => "top",
                    'theme_advanced_statusbar_location' => "bottom",
                    'spellchecker_rpc_url' => $CFG->wwwroot."/lib/editor/tinymce/tiny_mce/$this->version/plugins/spellchecker/rpc.php",
                    'spellchecker_languages' => get_config('editor_tinymce', 'spelllanguagelist')
                  );

        if ($xemoticon) {
            $manager = get_emoticon_manager();
            $emoticons = $manager->get_emoticons();
            $imgs = array();
            // see the TinyMCE plugin moodleemoticon for how the emoticon index is (ab)used :-S
            $index = 0;
            foreach ($emoticons as $emoticon) {
                $imgs[$emoticon->text] = $OUTPUT->render(
                    $manager->prepare_renderable_emoticon($emoticon, array('class' => 'emoticon emoticon-index-'.$index++)));
            }
            $params['moodleemoticon_emoticons'] = json_encode($imgs);
        }

        if (empty($CFG->xmlstrictheaders) and (!empty($options['legacy']) or !empty($options['noclean']) or !empty($options['trusted']))) {
            // now deal somehow with non-standard tags, people scream when we do not make moodle code xtml strict,
            // but they scream even more when we strip all tags that are not strict :-(
            $params['valid_elements'] = 'script[src|type],*[*]'; // for some reason the *[*] does not inlcude javascript src attribute MDL-25836
            $params['invalid_elements'] = '';
        }

        if (empty($options['legacy'])) {
            if (isset($options['maxfiles']) and $options['maxfiles'] != 0) {
                $params['file_browser_callback'] = "M.editor_tinymce.filepicker";
            }
        }
        //Add onblur event for client side text validation
        if (!empty($options['required'])) {
            $params['init_instance_callback'] = 'M.editor_tinymce.onblur_event';
        }
        return $params;
    }
}
