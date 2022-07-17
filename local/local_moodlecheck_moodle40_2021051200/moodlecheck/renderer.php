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
 * Renderer for local_moodlecheck
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Renderer for displaying local_moodlecheck
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_moodlecheck_renderer extends plugin_renderer_base {

    /** @var int $errorcount */
    protected $errorcount = 0;

    /**
     * Generates html to display one path validation results (invoked recursively)
     *
     * @param local_moodlecheck_path $path
     * @param string $format display format: html, xml, text
     * @return string
     */
    public function display_path(local_moodlecheck_path $path, $format = 'html') {
        $output = '';
        $path->validate();
        if ($path->is_dir()) {
            if ($format == 'html') {
                $output .= html_writer::start_tag('li', array('class' => 'directory'));
                $output .= html_writer::tag('span', $path->get_path(), array('class' => 'dirname'));
                $output .= html_writer::start_tag('ul', array('class' => 'directory'));
            } else if ($format == 'xml' && $path->is_rootpath()) {
                // Insert XML preamble and root element.
                $output .= '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
                    '<checkstyle version="1.3.2">' . PHP_EOL;
            }
            foreach ($path->get_subpaths() as $subpath) {
                $output .= $this->display_path($subpath, $format);
            }
            if ($format == 'html') {
                $output .= html_writer::end_tag('li');
                $output .= html_writer::end_tag('ul');
            } else if ($format == 'xml' && $path->is_rootpath()) {
                // Close root element.
                $output .= '</checkstyle>';
            }
        } else if ($path->is_file() && $path->get_file()->needs_validation()) {
            $output .= $this->display_file_validation($path->get_path(), $path->get_file(), $format);
        }
        return $output;
    }

    /**
     * Generates html to display one file validation results
     *
     * @param string $filename
     * @param local_moodlecheck_file $file
     * @param string $format display format: html, xml, text
     * @return string
     */
    public function display_file_validation($filename, local_moodlecheck_file $file, $format = 'html') {
        $output = '';
        $errors = $file->validate();
        $this->errorcount += count($errors);
        if ($format == 'html') {
            $output .= html_writer::start_tag('li', array('class' => 'file'));
            $output .= html_writer::tag('span', $filename, array('class' => 'filename'));
            $output .= html_writer::start_tag('ul', array('class' => 'file'));
        } else if ($format == 'xml') {
            $output .= html_writer::start_tag('file', array('name' => $filename)). "\n";
        } else if ($format == 'text') {
            $output .= $filename. "\n";
        }
        foreach ($errors as $error) {
            if (($format == 'html' || $format == 'text') && isset($error['line']) && strlen($error['line'])) {
                $error['message'] = get_string('linenum', 'local_moodlecheck', $error['line']). $error['message'];
            }
            if ($format == 'html') {
                $output .= html_writer::tag('li', $error['message'], array('class' => 'errorline'));
            } else {
                $error['message'] = strip_tags($error['message']);
                if ($format == 'text') {
                    $output .= "    ". $error['message']. "\n";
                } else if ($format == 'xml') {
                    $output .= '  '.html_writer::empty_tag('error', $error). "\n";
                }
            }
        }
        if ($format == 'html') {
            $output .= html_writer::end_tag('ul');
            $output .= html_writer::end_tag('li');
        } else if ($format == 'xml') {
            $output .= html_writer::end_tag('file'). "\n";
        }
        return $output;
    }

    /**
     * Display report summary
     *
     * @return string
     */
    public function display_summary() {
        if ($this->errorcount > 0) {
            return html_writer::tag('h2', get_string('notificationerror', 'local_moodlecheck', $this->errorcount),
                ['class' => 'fail']);
        } else {
            return html_writer::tag('h2', get_string('notificationsuccess', 'local_moodlecheck'), ['class' => 'good']);
        }
    }
}