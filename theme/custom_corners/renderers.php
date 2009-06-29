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
 * Functions for generating the HTML that Moodle should output.
 *
 * Please see http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 * for an overview.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */


/**
 * A renderer for the custom corner theme, and other themes based on it.
 *
 * Generates the slightly different HTML that the custom corners theme wants.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class custom_corners_core_renderer extends moodle_core_renderer {
    protected $wraplevel = 1;

    protected function custom_corners_divs($classes = '', $idbase = '') {
        if (strpos($classes, 'clearfix') !== false) {
            $clearfix = ' clearfix';
            $classes = trim(str_replace('clearfix', '', $classes));
        } else {
            $clearfix = '';
        }

        // Analise if we want ids for the custom corner elements
        $id = '';
        $idbt = '';
        $idi1 = '';
        $idi2 = '';
        $idi3 = '';
        $idbb = '';
        if ($idbase) {
            $id = $idbase;
            $idbt = $idbase . '-bt';
            $idi1 = $idbase . '-i1';
            $idi2 = $idbase . '-i2';
            $idi3 = $idbase . '-i3';
            $idbb = $idbase . '-bb';
        }

        // Create start tags.
        $start = $this->output_start_tag('div', array('id' => $id, 'class' => "wrap wraplevel{$this->wraplevel} $classes")) . "\n";
        $start .= $this->output_tag('div', array('id' => $idbt, 'class' => 'bt'), '<div>&nbsp;</div>') . "\n";
        $start .= $this->output_start_tag('div', array('id' => $idi1, 'class' => 'i1'));
        $start .= $this->output_start_tag('div', array('id' => $idi2, 'class' => 'i2'));
        $start .= $this->output_start_tag('div', array('id' => $idi3, 'class' => "i3$clearfix"));

        // Create end tags.
        $end = $this->output_end_tag('div');
        $end .= $this->output_end_tag('div');
        $end .= $this->output_end_tag('div');
        $end .= $this->output_tag('div', array('id' => $idbb, 'class' => 'bb'), '<div>&nbsp;</div>') . "\n";
        $end .= $this->output_end_tag('div');

        return array($start, $end);
    }

    public function box_start($classes = 'generalbox', $id = '') {
        list($start, $end) = $this->custom_corners_divs('ccbox box ' . moodle_renderer_base::prepare_classes($classes), $id);
        $this->opencontainers->push('box', $end);
        $this->wraplevel += 1;
        return $start;
    }

    public function box_end() {
        $this->wraplevel -= 1;
        return parent::box_end();
    }

    public function container_start($classes = '', $id = '') {
        list($start, $end) = $this->custom_corners_divs(moodle_renderer_base::prepare_classes($classes), $id);
        $this->opencontainers->push('container', $end);
        $this->wraplevel += 1;
        return $start;
    }

    public function container_end() {
        $this->wraplevel -= 1;
        return parent::container_end();
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * The content is described
     * by a {@link block_contents} object.
     *
     * @param block $content HTML for the content
     * @return string the HTML to be output.
     */
    function block($bc) {
        $bc = clone($bc);
        $bc->prepare();

        $title = strip_tags($bc->title);
        if (empty($title)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = $this->output_tag('a', array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'),
                    get_string('skipa', 'access', $title));
            $skipdest = $this->output_tag('span', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'), '');
        }

        $bc->attributes['id'] = $bc->id;
        $bc->attributes['class'] = $bc->get_classes_string();
        $output .= $this->output_start_tag('div', $bc->attributes);
        $output .= $this->output_start_tag('div', array('class' => 'wrap'));

        if ($bc->heading) {
            // Some callers pass in complete html for the heading, which may include
            // complicated things such as the 'hide block' button; some just pass in
            // text. If they only pass in plain text i.e. it doesn't include a
            // <div>, then we add in standard tags that make it look like a normal
            // page block including the h2 for accessibility
            if (strpos($bc->heading, '</div>') === false) {
                $bc->heading = $this->output_tag('div', array('class' => 'title'),
                        $this->output_tag('h2', null, $bc->heading));
            }

            $output .= '<div class="header"><div class="bt"><div>&nbsp;</div></div>';
            $output .= '<div class="i1"><div class="i2"><div class="i3">';
            $output .= $bc->heading;
            $output .= '</div></div></div></div>';
        } else {
            $output .= '<div class="bt"><div>&nbsp;</div></div>';
        }

        $output .= '<div class="i1"><div class="i2"><div class="i3"><div class="content">';

        if ($bc->content) {
            $output .= $bc->content;

        } else if ($bc->list) {
            $row = 0;
            $output .= $this->output_start_tag('ul', array('class' => 'list'));
            $items = array();
            foreach ($bc->list as $key => $string) {
                $item = $this->output_start_tag('li', array('class' => 'r' . $row));
                if ($bc->icons) {
                    $item .= $this->output_tag('div', array('class' => 'icon column c0'), $bc->icons[$key]);
                }
                $item .= $this->output_tag('div', array('class' => 'column c1'), $string);
                $item .= $this->output_end_tag('li');
                $items[] = $item;
                $row = 1 - $row; // Flip even/odd.
            }
            $output .= $this->output_tag('ul', array('class' => 'list'), implode("\n", $items));
        }

        if ($bc->footer) {
            $output .= $this->output_tag('div', array('class' => 'footer'), $bc->footer);
        }

        $output .= '</div></div></div></div><div class="bb"><div>&nbsp;</div></div></div></div>';
        $output .= $skipdest;

        if (!empty($CFG->allowuserblockhiding) && isset($attributes['id'])) {
            $strshow = addslashes_js(get_string('showblocka', 'access', $title));
            $strhide = addslashes_js(get_string('hideblocka', 'access', $title));
            $output .= $this->page->requires->js_function_call('elementCookieHide', array(
                    $bc->id, $strshow, $strhide))->asap();
        }

        return $output;
    }


}
