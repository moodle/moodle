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
 * Output rendering of Language customization admin tool
 *
 * @package    tool
 * @subpackage customlang
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Rendering methods for the tool widgets
 */
class tool_customlang_renderer extends plugin_renderer_base {

    /**
     * Renders customlang tool menu
     *
     * @return string HTML
     */
    protected function render_tool_customlang_menu(tool_customlang_menu $menu) {
        $output = '';
        foreach ($menu->get_items() as $item) {
            $output .= $this->single_button($item->url, $item->title, $item->method);
        }
        return $this->box($output, 'menu');
    }

    /**
     * Renders customlang translation table
     *
     * @param tool_customlang_translator $translator
     * @return string HTML
     */
    protected function render_tool_customlang_translator(tool_customlang_translator $translator) {
        $output = '';

        if (empty($translator->strings)) {
            return $this->notification(get_string('nostringsfound', 'tool_customlang'));
        }

        $table = new html_table();
        $table->id = 'translator';
        $table->head = array(
            get_string('headingcomponent', 'tool_customlang'),
            get_string('headingstringid', 'tool_customlang'),
            get_string('headingstandard', 'tool_customlang'),
            get_string('headinglocal', 'tool_customlang'),
        );

        foreach ($translator->strings as $string) {
            $cells = array();
            // component name
            $cells[0] = new html_table_cell($string->component);
            $cells[0]->attributes['class'] = 'component';
            // string identification code
            $cells[1] = new html_table_cell(html_writer::tag('div', s($string->stringid), array('class' => 'stringid')));
            $cells[1]->attributes['class'] = 'stringid';
            // master translation of the string
            $master = html_writer::tag('div', s($string->master), array('class' => 'preformatted'));
            $minheight = strlen($string->master) / 200;
            if (preg_match('/\{\$a(->.+)?\}/', $string->master)) {
                $master .= html_writer::tag('div', $this->help_icon('placeholder', 'tool_customlang',
                        get_string('placeholderwarning', 'tool_customlang')), array('class' => 'placeholderinfo'));
            }
            $cells[2] = new html_table_cell($master);
            $cells[2]->attributes['class'] = 'standard master';
            // local customization of the string
            $textareaattributes = array('name'=>'cust['.$string->id.']', 'cols'=>40, 'rows'=>3);
            if ($minheight>1) {
               $textareaattributes['style'] = 'min-height:' . (int) 4*$minheight . 'em;';
            }
            $textarea = html_writer::tag('textarea', s($string->local), $textareaattributes);
            $cells[3] = new html_table_cell($textarea);
            if (!is_null($string->local) and $string->outdated) {
                $mark  = html_writer::empty_tag('input', array('type' => 'checkbox', 'id' => 'update_' . $string->id,
                                                               'name' => 'updates[]', 'value' => $string->id));
                $help  = $this->help_icon('markinguptodate', 'tool_customlang');
                $mark .= html_writer::tag('label', get_string('markuptodate', 'tool_customlang') . $help,
                                          array('for' => 'update_' . $string->id));
                $mark  = html_writer::tag('div', $mark, array('class' => 'uptodatewrapper'));
            } else {
                $mark  = '';
            }
            $cells[3] = new html_table_cell($textarea."\n".$mark);
            $cells[3]->attributes['class'] = 'local';
            $cells[3]->id = 'id_'.$string->id;
            if (!is_null($string->local)) {
                $cells[3]->attributes['class'] .= ' customized';
            }
            if ($string->outdated) {
                $cells[3]->attributes['class'] .= ' outdated';
            }
            if ($string->modified) {
                $cells[3]->attributes['class'] .= ' modified';
            }

            if ($string->original !== $string->master) {
                $cells[0]->rowspan = $cells[1]->rowspan = $cells[3]->rowspan = 2;
            }

            $row = new html_table_row($cells);
            $table->data[] = $row;

            if ($string->original !== $string->master) {
                $cells = array();
                // original of the string
                $cells[2] = new html_table_cell(html_writer::tag('div', s($string->original), array('class' => 'preformatted')));
                $cells[2]->attributes['class'] = 'standard original';
                $row = new html_table_row($cells);
                $table->data[] = $row;
            }
        }

        $output .= html_writer::start_tag('form', array('method'=>'post', 'action'=>$translator->handler->out()));
        $output .= html_writer::start_tag('div');
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'translatorsubmitted', 'value'=>1));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));
        $save1   = html_writer::empty_tag('input', array('type'=>'submit', 'name'=>'savecontinue', 'value'=>get_string('savecontinue', 'tool_customlang')));
        $save2   = html_writer::empty_tag('input', array('type'=>'submit', 'name'=>'savecheckin', 'value'=>get_string('savecheckin', 'tool_customlang')));
        $output .= html_writer::tag('fieldset', $save1.$save2, array('class'=>'buttonsbar'));
        $output .= html_writer::table($table);
        $output .= html_writer::tag('fieldset', $save1.$save2, array('class'=>'buttonsbar'));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');

        return $output;
    }
}
