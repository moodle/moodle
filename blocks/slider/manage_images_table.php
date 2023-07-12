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
 * Simple slider block for Moodle
 *
 * If You like my plugin please send a small donation https://paypal.me/limsko Thanks!
 *
 * @package   block_slider
 * @copyright 2015-2020 Kamil Łuczak    www.limsko.pl     kamil@limsko.pl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}

/**
 * Class manage_images
 */
class manage_images extends table_sql {

    /**
     * manage_images constructor.
     * @param $uniqueid
     * @throws coding_exception
     */
    public function __construct($uniqueid) {
        parent::__construct($uniqueid);

        // Define the list of columns to show.
        $columns = array('slide_order', 'slide_link', 'slide_title', 'slide_desc', 'slide_image', 'manage');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array(
                get_string('slide_order', 'block_slider'),
                get_string('slide_url', 'block_slider'),
                get_string('slide_title', 'block_slider'),
                get_string('slide_desc', 'block_slider'),
                get_string('slide_image', 'block_slider'),
                get_string('manage_slides', 'block_slider'),
        );
        $this->define_headers($headers);
    }

    /**
     * Column with slide image.
     *
     * @param $values
     * @return string
     */
    public function col_slide_image($values) {
        global $CFG, $context;
        // If the data is being downloaded than we don't want to show HTML.
        $url = $CFG->wwwroot . '/pluginfile.php/' . $context->id . '/block_slider/slider_slides/' . $values->id . '/' .
                $values->slide_image;
        return html_writer::empty_tag('img', array('src' => $url, 'class' => 'img-thumbnail'));
    }

    /**
     * Column with manage buttons.
     *
     * @param $values
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function col_manage($values) {
        $editurl = new moodle_url('/blocks/slider/manage_images.php', array('id' => $values->id, 'sliderid' => $values->sliderid));
        $editbtn = html_writer::tag('a', get_string('edit'), array('href' => $editurl, 'class' => 'btn btn-primary'));
        $deleteurl =
                new moodle_url('/blocks/slider/delete_image.php', array('id' => $values->id, 'sliderid' => $values->sliderid));
        $deletebtn = html_writer::tag('a', get_string('delete'), array('href' => $deleteurl, 'class' => 'btn btn-primary'));
        return "<p>$editbtn</p><p>$deletebtn</p>";
    }
}