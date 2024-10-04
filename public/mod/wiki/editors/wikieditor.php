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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains all necessary code to define a wiki editor
 *
 * @package mod_wiki
 * @copyright 2009 Marc Alier, Jordi Piguillem marc.alier@upc.edu
 * @copyright 2009 Universitat Politecnica de Catalunya http://www.upc.edu
 *
 * @author Josep Arus
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/lib/formslib.php');
require_once($CFG->dirroot.'/lib/form/textarea.php');
require_once($CFG->dirroot.'/lib/form/templatable_form_element.php');

class MoodleQuickForm_wikieditor extends MoodleQuickForm_textarea {
    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    private $files;

    /** @var string */
    protected $wikiformat;

    /**
     * Constructor
     *
     * @param string $elementName (optional) name of the text field
     * @param string $elementLabel (optional) text field label
     * @param string $attributes (optional) Either a typical HTML attribute string or an associative array
     */
    function __construct($elementName = null, $elementLabel = null, $attributes = null) {
        if (isset($attributes['wiki_format'])) {
            $this->wikiformat = $attributes['wiki_format'];
            unset($attributes['wiki_format']);
        }
        if (isset($attributes['files'])) {
            $this->files = $attributes['files'];
            unset($attributes['files']);
        }
        parent::__construct($elementName, $elementLabel, $attributes);
        $this->_type = 'wikieditor';
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_wikieditor($elementName = null, $elementLabel = null, $attributes = null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $attributes);
    }

    function setWikiFormat($wikiformat) {
        $this->wikiformat = $wikiformat;
    }

    function toHtml() {
        $textarea = parent::toHtml();

        return $this->{
            $this->wikiformat."Editor"}
            ($textarea);
    }

    function creoleEditor($textarea) {
        return $this->printWikiEditor($textarea);
    }

    function nwikiEditor($textarea) {
        return $this->printWikiEditor($textarea);
    }

    private function printWikiEditor($textarea) {
        global $OUTPUT;

        $textarea = $OUTPUT->container_start().$textarea.$OUTPUT->container_end();

        $buttons = $this->getButtons();

        return $buttons.$textarea;
    }

    private function getButtons() {
        global $PAGE, $OUTPUT, $CFG;

        $editor = $this->wikiformat;

        $tag = $this->getTokens($editor, 'bold');
        $wiki_editor['bold'] = array('ed_bold.gif', get_string('wikiboldtext', 'wiki'), $tag[0], $tag[1], get_string('wikiboldtext', 'wiki'));

        $tag = $this->getTokens($editor, 'italic');
        $wiki_editor['italic'] = array('ed_italic.gif', get_string('wikiitalictext', 'wiki'), $tag[0], $tag[1], get_string('wikiitalictext', 'wiki'));

        $imagetag = $this->getTokens($editor, 'image');
        $wiki_editor['image'] = array('ed_img.gif', get_string('wikiimage', 'wiki'), $imagetag[0], $imagetag[1], get_string('wikiimage', 'wiki'));

        $tag = $this->getTokens($editor, 'link');
        $wiki_editor['internal'] = array('ed_internal.gif', get_string('wikiinternalurl', 'wiki'), $tag[0], $tag[1], get_string('wikiinternalurl', 'wiki'));

        $tag = $this->getTokens($editor, 'url');
        $wiki_editor['external'] = array('ed_external.gif', get_string('wikiexternalurl', 'wiki'), $tag, "", get_string('wikiexternalurl', 'wiki'));

        $tag = $this->getTokens($editor, 'list');
        $wiki_editor['u_list'] = array('ed_ul.gif', get_string('wikiunorderedlist', 'wiki'), '\\n'.$tag[0], '', '');
        $wiki_editor['o_list'] = array('ed_ol.gif', get_string('wikiorderedlist', 'wiki'), '\\n'.$tag[1], '', '');

        $tag = $this->getTokens($editor, 'header');
        $wiki_editor['h1'] = array('ed_h1.gif', get_string('wikiheader', 'wiki', 1), '\\n'.$tag.' ', ' '.$tag.'\\n', get_string('wikiheader', 'wiki', 1));
        $wiki_editor['h2'] = array('ed_h2.gif', get_string('wikiheader', 'wiki', 2), '\\n'.$tag.$tag.' ', ' '.$tag.$tag.'\\n', get_string('wikiheader', 'wiki', 2));
        $wiki_editor['h3'] = array('ed_h3.gif', get_string('wikiheader', 'wiki', 3), '\\n'.$tag.$tag.$tag.' ', ' '.$tag.$tag.$tag.'\\n', get_string('wikiheader', 'wiki', 3));

        $tag = $this->getTokens($editor, 'line_break');
        $wiki_editor['hr'] = array('ed_hr.gif', get_string('wikihr', 'wiki'), '\\n'.$tag.'\\n', '', '');

        $tag = $this->getTokens($editor, 'nowiki');
        $wiki_editor['nowiki'] = array('ed_nowiki.gif', get_string('wikinowikitext', 'wiki'), $tag[0], $tag[1], get_string('wikinowikitext', 'wiki'));

        $PAGE->requires->js('/mod/wiki/editors/wiki/buttons.js');

        $html = '<div class="wikieditor-toolbar">';
        foreach ($wiki_editor as $button) {
            $html .= "<a href=\"javascript:insertTags";
            $html .= "('".$button[2]."','".$button[3]."','".$button[4]."');\">";
            $html .= html_writer::empty_tag('img', array('alt' => $button[1], 'src' => $CFG->wwwroot . '/mod/wiki/editors/wiki/images/' . $button[0]));
            $html .= "</a>";
        }
        $html .= "<label class='accesshide' for='addtags'>" . get_string('insertimage', 'wiki')  . "</label>";
        $html .= "<select id='addtags' class='form-select mx-1' " .
                 "onchange=\"insertTags('{$imagetag[0]}', '{$imagetag[1]}', this.value)\">";
        $html .= "<option value='" . s(get_string('wikiimage', 'wiki')) . "'>" . get_string('insertimage', 'wiki') . '</option>';
        foreach ($this->files as $filename) {
            $html .= "<option value='".s($filename)."'>";
            $html .= $filename;
            $html .= '</option>';
        }
        $html .= '</select>';
        $html .= $OUTPUT->help_icon('insertimage', 'wiki');
        $html .= '</div>';

        return $html;
    }

    private function getTokens($format, $token) {
        $tokens = wiki_parser_get_token($format, $token);

        if (is_array($tokens)) {
            foreach ($tokens as & $t) {
                $this->escapeToken($t);
            }
        } else {
            $this->escapeToken($tokens);
        }

        return $tokens;
    }

    private function escapeToken(&$token) {
        $token = urlencode(str_replace("'", "\'", $token));
    }

    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);

        // We do want the form-control class on the output from toHTML - but we dont' want it when calling export_for_template.
        // This is because in this type of form element the export_for_template calls toHTML to get the html for the context.
        // If we did both we would be duplicating the form-control which messes up the styles.
        $saved = $this->getAttribute('class');
        $this->updateAttributes(['class' => $saved . ' form-control']);

        $context['html'] = $this->toHtml();
        $this->updateAttributes(['class' => $saved]);

        return $context;
    }
}

//register wikieditor
MoodleQuickForm::registerElementType('wikieditor', $CFG->dirroot."/mod/wiki/editors/wikieditor.php", 'MoodleQuickForm_wikieditor');
