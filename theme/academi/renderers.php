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
 * Version details
 *
 * @package    theme
 * @subpackage academi
 * @copyright  2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once($CFG->dirroot.'/blocks/course_overview/locallib.php');
require_once($CFG->dirroot . "/course/renderer.php");
require_once($CFG->libdir. '/coursecatlib.php');

class theme_academi_core_renderer extends theme_bootstrapbase_core_renderer {
}

class theme_academi_core_course_renderer extends core_course_renderer {
	protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
		global $CFG;
	
		if (!isset($this->strings->summary)) {
			$this->strings->summary = get_string('summary');
		}
		if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
			return '';
		}
		if ($course instanceof stdClass) {
			require_once($CFG->libdir. '/coursecatlib.php');
			$course = new course_in_list($course);
		}
		
		$content = '';
	
		$content .= html_writer::start_tag('div', array('class' => 'span2', 'style' => 'min-width: 19%; margin: 10px 0px 10px 1%;'));
		$url_course_info = new moodle_url('/course/info.php', array('id' => $course->id));
		
		$content .= html_writer::start_tag('div', array('class' => 'frontpage-coruse'));
		
		$coursename = $chelper->get_course_formatted_name($course);
		
		$url_pic = '';
		foreach ($course->get_course_overviewfiles() as $file) {
			$isimage = $file->is_valid_image();
			$url_pic = file_encode_url("$CFG->wwwroot/pluginfile.php",
					'/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
					$file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
			if ($isimage) {
				break;
			}
		}
		$content .= html_writer::tag(
			'a',
			html_writer::empty_tag(
				'img',
				array('src' => $url_pic, 'alt' => $coursename)
			),
			array('href' => $url_course_info)
		);
		
		$content .= html_writer::start_tag('p');
		$content .= $coursename;
		$content .= html_writer::end_tag('p'); // End p.
		
		$content .= html_writer::end_tag('div'); // End .frontpage-coruse.
		
		$content .= html_writer::end_tag('div'); // End .span2.
		return $content;
	}
}