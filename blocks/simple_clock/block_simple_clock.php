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
 * Simple Clock block definition
 *
 * @package    contrib
 * @subpackage block_simple_clock
 * @copyright  2010 Michael de Raadt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

// There are three possible states and we must show at least one clock.
define('B_SIMPLE_CLOCK_SHOW_BOTH',        0);
define('B_SIMPLE_CLOCK_SHOW_SERVER_ONLY', 1);
define('B_SIMPLE_CLOCK_SHOW_USER_ONLY',   2);

/**
 * Simple clock block class
 *
 * @copyright 2010 Michael de Raadt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_simple_clock extends block_base {

    /**
     * Sets the block title
     *
     * @return none
     */
    public function init() {
        $this->title = get_string('clock_title_default', 'block_simple_clock');
    }

    /**
     * Controls the block title based on instance configuration
     *
     * @return bool
     */
    public function specialization() {
        // Override the block title if an alternative is set.
        if (isset($this->config->clock_title) && trim($this->config->clock_title) != '') {
            $this->title = format_string($this->config->clock_title);
        }
    }

    /**
     * Defines where the block can be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'course-view'    => true,
            'site-index'     => true,
            'mod'            => true,
            'my'             => true
        );
    }

    /**
     * Controls global configurability of block
     *
     * @return bool
     */
    public function instance_allow_config() {
        return false;
    }

    /**
     * Controls global configurability of block
     *
     * @return bool
     */
    public function has_config() {
        return false;
    }

    /**
     * Controls if a block header is shown based on instance configuration
     *
     * @return bool
     */
    public function hide_header() {
        return isset($this->config->show_header) && $this->config->show_header == 0;
    }

    /**
     * Creates the block's main content
     *
     * @return string
     */
    public function get_content() {

        global $USER, $OUTPUT, $CFG;

        if (isset($this->content)) {
            return $this->content;
        }

        // Establish settings variables based on instance config.
        $showserverclock = !isset($this->config->show_clocks) ||
            $this->config->show_clocks == B_SIMPLE_CLOCK_SHOW_BOTH ||
            $this->config->show_clocks == B_SIMPLE_CLOCK_SHOW_SERVER_ONLY;
        $showuserclock = !isset($this->config->show_clocks) ||
            $this->config->show_clocks == B_SIMPLE_CLOCK_SHOW_BOTH ||
            $this->config->show_clocks == B_SIMPLE_CLOCK_SHOW_USER_ONLY;
        $showicons = !isset($this->config->show_icons) || $this->config->show_icons == 1;
        $showseconds = isset($this->config->show_seconds) && $this->config->show_seconds == 1;
        $showday = isset($this->config->show_day) && $this->config->show_day == 1;
        $show24hrtime = isset($this->config->twenty_four_hour_time) && $this->config->twenty_four_hour_time == 1;

        // Start the content, which is primarily a table.
        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        $table = new html_table();
        $table->attributes = array('class' => 'clockTable');

        // First item added is the server's clock.
        if ($showserverclock) {
            $row = array();
            if ($showicons) {
                $alt = get_string('server', 'block_simple_clock');
                $usingie = false;
                if(class_exists('core_useragent')) {
                    $usingie = core_useragent::is_ie();
                }
                else {
                    $usingie = check_browser_version('MSIE');
                }
                if ($usingie) {
                    $servericon = $OUTPUT->pix_icon('server', $alt, 'block_simple_clock');
                }
                else {
                    $servericon = $OUTPUT->pix_icon('favicon', $alt, 'theme');
                }
                $row[] = $servericon;
            }
            $row[] = get_string('server', 'block_simple_clock').':';
            $attributes = array();
            $attributes['class'] = 'clock';
            $attributes['id'] = 'block_progress_serverTime';
            $attributes['value'] = get_string('loading', 'block_simple_clock');
            $row[] = HTML_WRITER::empty_tag('input', $attributes);
            $table->data[] = $row;
        }

        // Next item is the user's clock.
        if ($showuserclock) {
            $row = array();
            if ($showicons) {
                if ($USER->id != 0) {
                    $userpictureparams = array('size' => 16, 'link' => false, 'alt' => 'User');
                    $userpicture = $OUTPUT->user_picture($USER, $userpictureparams);
                    $row[] = $userpicture;
                }
                else {
                    $row[] = '';
                }
            }
            $row[] = get_string('you', 'block_simple_clock').':';
            $attributes = array();
            $attributes['class'] = 'clock';
            $attributes['id'] = 'block_progress_youTime';
            $attributes['value'] = get_string('loading', 'block_simple_clock');
            $row[] = HTML_WRITER::empty_tag('input', $attributes);
            $table->data[] = $row;
        }
        $this->content->text .= HTML_WRITER::table($table);

        // Set up JavaScript code needed to keep the clock going.
        $noscriptstring = get_string('javascript_disabled', 'block_simple_clock');
        $this->content->text .= HTML_WRITER::tag('noscript', $noscriptstring);
        if ($CFG->timezone != 99) {
            // Ensure that the Moodle timezone is set correctly.
            $date = new DateTime('now', new DateTimeZone(core_date::normalise_timezone($CFG->timezone)));
            $moodletimeoffset = $date->getOffset(); // + dst_offset_on(time(), $CFG->timezone);
            $servertimeoffset = date_offset_get(new DateTime);
            $timearray = localtime(time() + $moodletimeoffset - $servertimeoffset, true);
        }
        else {
            // Ensure that the server timezone is set.
            // From 2.9 onwards, this should never happen.
            $timearray = localtime(time(), true);
        }

        $arguments = array(
            $showserverclock,
            $showuserclock,
            $showseconds,
            $showday,
            $show24hrtime,
            $timearray['tm_year'] + 1900,
            $timearray['tm_mon'],
            $timearray['tm_mday'],
            $timearray['tm_hour'],
            $timearray['tm_min'],
            $timearray['tm_sec'] + 2 // Arbitrary load time added.
        );
        $jsmodule = array(
            'name' => 'block_simple_clock',
            'fullpath' => '/blocks/simple_clock/module.js',
            'requires' => array(),
            'strings' => array(
                array('clock_separator', 'block_simple_clock'),
                array('before_noon', 'block_simple_clock'),
                array('after_noon', 'block_simple_clock'),
                array('day_names', 'block_simple_clock'),
            ),
        );
        $this->page->requires->js_init_call('M.block_simple_clock.initSimpleClock',
                                            $arguments, false, $jsmodule);

        $this->content->footer = '';
        return $this->content;
    }
}
