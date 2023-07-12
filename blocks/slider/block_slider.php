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
 * Class block_slider
 */
class block_slider extends block_base {

    public $hasslides = false;

    /**
     * Initializes block.
     *
     * @throws coding_exception
     */
    public function init() {
        global $DB;
        $this->title = get_string('pluginname', 'block_slider');
    }

    /**
     * Returns content of block.
     *
     * @return stdClass|stdObject
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_content() {
        global $CFG, $DB, $bxs;
        require_once($CFG->libdir . '/filelib.php');
        require_once($CFG->dirroot . '/blocks/slider/lib.php');

        if ($this->content !== null) {
            return $this->content;
        }

        // Prepare slides.
        if ($slides = $DB->get_records('slider_slides', array('sliderid' => $this->instance->id), 'slide_order ASC')) {
            $this->hasslides = $slides;
        }

        $this->content = new stdClass;
        $bxslider = false;
        if (isset($this->config->slider_js) && trim($this->config->slider_js) === 'bxslider') {
            $bxslider = true;
        }

        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            $this->content->text = '';
        }

        if (!isset($bxs)) {
            $bxs = 1;
        } else {
            $bxs++;
        }
        $this->content->text .= '<div class="slider"><div id="slides' . $this->instance->id . $bxs . '" ';

        if (!$bxslider) {
            $this->content->text .= 'style="display: none;" class="slides' . $this->instance->id . $bxs . '"';
        } else {
            $this->content->text .= 'class="bxslider bxslider' . $this->instance->id . $bxs . '" style="visibility: hidden;"';
        }
        $this->content->text .= '>';

        $this->content->text .= $this->display_images($bxslider);

        // Navigation Left/Right.
        if (!empty($this->config->navigation) && !$bxslider && $this->hasslides) {
            $this->content->text .= '<a href="#" class="slidesjs-previous slidesjs-navigation">
    <i class="icon fa fa-chevron-left icon-large" aria-hidden="true" aria-label="Prev"></i></a>';
            $this->content->text .= '<a href="#" class="slidesjs-next slidesjs-navigation">
    <i class="icon fa fa-chevron-right icon-large" aria-hidden="true" aria-label="Next"></i></a>';
        }

        $this->content->text .= '</div></div>';

        if (!empty($this->config->width) and is_numeric($this->config->width)) {
            $width = $this->config->width;
        } else {
            $width = 940;
        }

        if (!empty($this->config->height) and is_numeric($this->config->height)) {
            $height = $this->config->height;
        } else {
            $height = 528;
        }

        if (!empty($this->config->interval) and is_numeric($this->config->interval)) {
            $interval = $this->config->interval;
        } else {
            $interval = 5000;
        }

        if (!empty($this->config->effect)) {
            $effect = $this->config->effect;
        } else {
            $effect = 'fade';
        }

        if (!empty($this->config->pagination)) {
            $pag = true;
        } else {
            $pag = false;
        }

        if (!empty($this->config->autoplay)) {
            $autoplay = true;
        } else {
            $autoplay = false;
        }

        $nav = false;

        if ($bxslider) {
            $this->page->requires->js_call_amd('block_slider/bxslider', 'init',
                    bxslider_get_settings($this->config, $this->instance->id . $bxs));
        } else {
            $this->page->requires->js_call_amd('block_slider/slides', 'init',
                    array($width, $height, $effect, $interval, $autoplay, $pag, $nav, $this->instance->id . $bxs));
        }
        // If user has capability of editing, add button.
        if (has_capability('block/slider:manage', $this->context)) {
            $instancearray = array('sliderid' => $this->instance->id);
            if (isset($this->page->course->id)) {
                $instancearray['course'] = $this->page->course->id;
            }
            $editurl = new moodle_url('/blocks/slider/manage_images.php', $instancearray);
            $this->content->footer = html_writer::tag('a', get_string('manage_slides', 'block_slider'),
                    array('href' => $editurl, 'class' => 'btn btn-primary'));

        }

        return $this->content;
    }

    /**
     * Generate html with slides.
     *
     * @param bool $bxslider
     * @return string
     */
    public function display_images($bxslider = false) {
        global $CFG;
        // Get and display images.
        $html = '';
        if ($this->hasslides) {
            foreach ($this->hasslides as $slide) {
                $imageurl = $CFG->wwwroot . '/pluginfile.php/' . $this->context->id . '/block_slider/slider_slides/' . $slide->id .
                        '/' . $slide->slide_image;
                if ($bxslider) {
                    $html .= html_writer::start_tag('div', ['class' => 'bxslide']);
                }
                if (!empty($slide->slide_link)) {
                    $html .= html_writer::start_tag('a', array('href' => $slide->slide_link, 'rel' => 'nofollow'));
                }
                $html .= html_writer::empty_tag('img',
                        array('src' => $imageurl,
                                'class' => 'img',
                                'alt' => $slide->slide_image,
                            // Title has been moved to html code.
                                'width' => '100%'));
                if (!empty($slide->slide_link)) {
                    $html .= html_writer::end_tag('a');
                }

                // Display captions in BxSlider mode.
                if ($bxslider) {
                    if ($this->config->bx_captions or $this->config->bx_displaydesc) {
                        $classes = '';
                        if ($this->config->bx_captions) {
                            $classes .= ' bxcaption';
                        }
                        if ($this->config->bx_displaydesc) {
                            $classes .= ' bxdesc';
                        }
                        if ($this->config->bx_hideonhover) {
                            $classes .= ' hideonhover';
                        }
                        $html .= html_writer::start_tag('div', array('class' => 'bx-caption' . $classes));
                        $html .= html_writer::tag('span', $slide->slide_title);
                        $html .= html_writer::tag('p', $slide->slide_desc);
                        $html .= html_writer::end_tag('div');
                    }

                    $html .= html_writer::end_tag('div');
                }
            }
        }

        return $html;
    }

    /**
     * This plugin has no global config.
     *
     * @return bool
     */
    public function has_config() {
        return false;
    }

    /**
     * We are legion.
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Where we can add the block?
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
                'site' => true,
                'course-view' => true,
                'my' => true
        );
    }

    /**
     * What happens when instance of block is deleted.
     *
     * @return bool
     * @throws dml_exception
     */
    public function instance_delete() {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/blocks/slider/lib.php');
        if ($slides = $DB->get_records('slider_slides', array('sliderid' => $this->instance->id))) {
            foreach ($slides as $slide) {
                block_slider_delete_slide($slide);
            }
        }
        return true;
    }

    /**
     * Hide header of this block when user is not editing.
     *
     * @return bool
     */
    public function hide_header() {
        if ($this->page->user_is_editing()) {
            return false;
        } else {
            return true;
        }
    }
}