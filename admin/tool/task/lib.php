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
 * Task API status checks
 *
 * @package    tool_task
 * @copyright  2020 Brendan Heywood (brendan@catalyst-au.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Add cron related service status checks
 *
 * @return array of check objects
 */
function tool_task_status_checks(): array {
    return [
        new \tool_task\check\cronrunning(),
        new \tool_task\check\maxfaildelay(),
        new \tool_task\check\adhocqueue(),
        new \tool_task\check\longrunningtasks(),
    ];
}

/**
 * Function used to handle mtrace by outputting the text to normal browser window.
 *
 * @param string $message Message to output
 * @param string $eol End of line character
 */
function tool_task_mtrace_wrapper(string $message, string $eol = ''): void {
    $message = s($message);

    // We autolink urls and emails here but can't use format_text as it does
    // more than we need and has side effects which are not useful in this context.
    $urlpattern = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
    $message = preg_replace_callback($urlpattern, function($matches) {
        $url = $matches[0];
        return html_writer::link($url, $url, ['target' => '_blank']);
    }, $message);

    $emailpattern = '/[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/';
    $message = preg_replace_callback($emailpattern, function($matches) {
        $email = $matches[0];
        return html_writer::link('mailto:' . $email, $email);
    }, $message);

    echo $message . $eol;
}
