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
 * Overriden block settings renderer.
 *
 * @package    theme_bootstrapbase
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_bootstrapbase\output;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/settings/renderer.php');

use moodle_url;

/**
 * Overriden block settings renderer.
 *
 * @package    theme_bootstrapbase
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_settings_renderer extends \block_settings_renderer {

    public function search_form(moodle_url $formtarget, $searchvalue) {
        $content = \html_writer::start_tag('form', array('class'=>'adminsearchform', 'method'=>'get', 'action'=>$formtarget, 'role' => 'search'));
        $content .= \html_writer::start_tag('div');
        $content .= \html_writer::tag('label', s(get_string('searchinsettings', 'admin')), array('for'=>'adminsearchquery', 'class'=>'accesshide'));
        $content .= \html_writer::empty_tag('input', array('id'=>'adminsearchquery', 'type'=>'text', 'name'=>'query', 'value'=>s($searchvalue)));
        $content .= \html_writer::empty_tag('input', array('type'=>'submit', 'value'=>s(get_string('search'))));
        $content .= \html_writer::end_tag('div');
        $content .= \html_writer::end_tag('form');
        return $content;
    }

}
