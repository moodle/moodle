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

namespace core\output;

use core_text;
use core\check\result as check_result;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/clilib.php");

/**
 * A renderer that generates output for command-line scripts.
 *
 * The implementation of this renderer is probably incomplete.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class core_renderer_cli extends core_renderer {
    /**
     * @var array $progressmaximums stores the largest percentage for a progress bar.
     */
    private $progressmaximums = [];

    /**
     * Returns the page header.
     *
     * @return string HTML fragment
     */
    public function header() {
        return $this->page->heading . "\n";
    }

    /**
     * Renders a Check API result
     *
     * To aid in CLI consistency this status is NOT translated and the visual
     * width is always exactly 10 chars.
     *
     * @param check_result $result
     * @return string HTML fragment
     */
    protected function render_check_result(check_result $result) {
        $status = $result->get_status();

        $labels = [
            check_result::NA        => '      ' . cli_ansi_format('<colour:darkGray>') . ' NA ',
            check_result::OK        => '      ' . cli_ansi_format('<colour:green>') . ' OK ',
            check_result::INFO      => '    '   . cli_ansi_format('<colour:blue>') . ' INFO ',
            check_result::UNKNOWN   => ' '      . cli_ansi_format('<colour:darkGray>') . ' UNKNOWN ',
            check_result::WARNING   => ' '      . cli_ansi_format('<colour:black><bgcolour:yellow>') . ' WARNING ',
            check_result::ERROR     => '   '    . cli_ansi_format('<bgcolour:red>') . ' ERROR ',
            check_result::CRITICAL  => ''       . cli_ansi_format('<bgcolour:red>') . ' CRITICAL ',
        ];
        $string = $labels[$status] . cli_ansi_format('<colour:normal>');
        return $string;
    }

    /**
     * Renders a Check API result
     *
     * @param check_result $result
     * @return string fragment
     */
    public function check_result(check_result $result) {
        return $this->render_check_result($result);
    }

    /**
     * Renders a progress bar.
     *
     * Do not use $OUTPUT->render($bar), instead use progress_bar::create().
     *
     * @param  progress_bar $bar The bar.
     * @return string ascii fragment
     */
    public function render_progress_bar(progress_bar $bar) {
        $size = 55; // The width of the progress bar in chars.
        $ascii = "\n";

        if (stream_isatty(STDOUT)) {
            $ascii .= "[" . str_repeat(' ', $size) . "] 0% \n";
            return cli_ansi_format($ascii);
        }

        $this->progressmaximums[$bar->get_id()] = 0;
        $ascii .= '[';
        return $ascii;
    }

    /**
     * Renders an update to a progress bar.
     *
     * Note: This does not cleanly map to a renderable class and should
     * never be used directly.
     *
     * @param  string $id
     * @param  float $percent
     * @param  string $msg Message
     * @param  string $estimate time remaining message
     * @param  bool $error (Unused in cli)
     * @return string ascii fragment
     */
    public function render_progress_bar_update(string $id, float $percent, string $msg, string $estimate,
        bool $error = false): string {
        $size = 55; // The width of the progress bar in chars.
        $ascii = '';

        // If we are rendering to a terminal then we can safely use ansii codes
        // to move the cursor and redraw the complete progress bar each time
        // it is updated.
        if (stream_isatty(STDOUT)) {
            $colour = $percent == 100 ? 'green' : 'blue';

            $done = $percent * $size * 0.01;
            $whole = floor($done);
            $bar = "<colour:$colour>";
            $bar .= str_repeat('█', $whole);

            if ($whole < $size) {
                // By using unicode chars for partial blocks we can have higher
                // precision progress bar.
                $fraction = floor(($done - $whole) * 8);
                $bar .= core_text::substr(' ▏▎▍▌▋▊▉', $fraction, 1);

                // Fill the rest of the empty bar.
                $bar .= str_repeat(' ', $size - $whole - 1);
            }

            $bar .= '<colour:normal>';

            if ($estimate) {
                $estimate = "- $estimate";
            }

            $ascii .= '<cursor:up>';
            $ascii .= '<cursor:up>';
            $ascii .= sprintf("[$bar] %3.1f%% %-22s\n", $percent, $estimate);
            $ascii .= sprintf("%-80s\n", $msg);
            return cli_ansi_format($ascii);
        }

        // If we are not rendering to a tty, ie when piped to another command
        // or on windows we need to progressively render the progress bar
        // which can only ever go forwards.
        $done = round($percent * $size * 0.01);
        $delta = max(0, $done - $this->progressmaximums[$id]);

        $ascii .= str_repeat('#', $delta);
        if ($percent >= 100 && $delta > 0) {
            $ascii .= sprintf("] %3.1f%%", $percent) . "\n$msg\n";
        }
        $this->progressmaximums[$id] += $delta;
        return $ascii;
    }

    /**
     * Returns a template fragment representing a Heading.
     *
     * @param string $text The text of the heading
     * @param int $level The level of importance of the heading
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string A template fragment for a heading
     */
    public function heading($text, $level = 2, $classes = 'main', $id = null) {
        $text .= "\n";
        switch ($level) {
            case 1:
                return '=>' . $text;
            case 2:
                return '-->' . $text;
            default:
                return $text;
        }
    }

    /**
     * Returns a template fragment representing a fatal error.
     *
     * @param string $message The message to output
     * @param string $moreinfourl URL where more info can be found about the error
     * @param string $link Link for the Continue button
     * @param array $backtrace The execution backtrace
     * @param null|string $debuginfo Debugging information
     * @param string $errorcode
     * @return string A template fragment for a fatal error
     */
    public function fatal_error($message, $moreinfourl, $link, $backtrace, $debuginfo = null, $errorcode = "") {
        global $CFG;

        $output = "!!! $message !!!\n";

        if ($CFG->debugdeveloper) {
            if (!empty($debuginfo)) {
                $output .= $this->notification($debuginfo, 'notifytiny');
            }
            if (!empty($backtrace)) {
                $output .= $this->notification('Stack trace: ' . format_backtrace($backtrace, true), 'notifytiny');
            }
        }

        return $output;
    }

    /**
     * Returns a template fragment representing a notification.
     *
     * @param string $message The message to print out.
     * @param string $type    The type of notification. See constants on \core\output\notification.
     * @param bool $closebutton Whether to show a close icon to remove the notification (default true).
     * @param string|null $title The title of the notification.
     * @param ?string $titleicon if the title should have an icon you can give the icon name with the component
     *  (e.g. 'i/circleinfo, core' or 'i/circleinfo' if the icon is from core)
     * @return string A template fragment for a notification
     */
    public function notification($message, $type = null, $closebutton = true, ?string $title = null, ?string $titleicon = null) {
        $message = clean_text($message);
        if ($type === 'notifysuccess' || $type === 'success') {
            return "++ $message ++\n";
        }
        return "!! $message !!\n";
    }

    /**
     * There is no footer for a cli request, however we must override the
     * footer method to prevent the default footer.
     */
    public function footer() {
    }

    /**
     * Render a notification (that is, a status message about something that has
     * just happened).
     *
     * @param notification $notification the notification to print out
     * @return string plain text output
     */
    public function render_notification(notification $notification) {
        return $this->notification($notification->get_message(), $notification->get_message_type());
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(core_renderer_cli::class, \core_renderer_cli::class);
