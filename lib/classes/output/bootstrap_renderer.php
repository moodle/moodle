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

use coding_exception;

/**
 * This class solves the problem of how to initialise $OUTPUT.
 *
 * The problem is caused be two factors
 * <ol>
 * <li>On the one hand, we cannot be sure when output will start. In particular,
 * an error, which needs to be displayed, could be thrown at any time.</li>
 * <li>On the other hand, we cannot be sure when we will have all the information
 * necessary to correctly initialise $OUTPUT. $OUTPUT depends on the theme, which
 * (potentially) depends on the current course, course categories, and logged in user.
 * It also depends on whether the current page requires HTTPS.</li>
 * </ol>
 *
 * So, it is hard to find a single natural place during Moodle script execution,
 * which we can guarantee is the right time to initialise $OUTPUT. Instead we
 * adopt the following strategy
 * <ol>
 * <li>We will initialise $OUTPUT the first time it is used.</li>
 * <li>If, after $OUTPUT has been initialised, the script tries to change something
 * that $OUTPUT depends on, we throw an exception making it clear that the script
 * did something wrong.
 * </ol>
 *
 * The only problem with that is, how do we initialise $OUTPUT on first use if,
 * it is going to be used like $OUTPUT->somthing(...)? Well that is where this
 * class comes in. Initially, we set up $OUTPUT = new bootstrap_renderer(). Then,
 * when any method is called on that object, we initialise $OUTPUT, and pass the call on.
 *
 * Note that this class is used before lib/outputlib.php has been loaded, so we
 * must be careful referring to classes/functions from there, they may not be
 * defined yet, and we must avoid fatal errors.
 *
 * @package    core
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class bootstrap_renderer {
    /**
     * Handles re-entrancy. Without this, errors or debugging output that occur
     * during the initialisation of $OUTPUT, cause infinite recursion.
     *
     * @var bool
     */
    protected $initialising = false;

    /**
     * Whether output has started yet.
     *
     * @return bool true if the header has been printed.
     */
    public function has_started() {
        return false;
    }

    /**
     * Constructor - to be used by core code only.
     *
     * @param string $method The method to call
     * @param array $arguments Arguments to pass to the method being called
     * @return string
     */
    public function __call($method, $arguments) {
        // phpcs:disable moodle.PHP.ForbiddenGlobalUse.BadGlobal
        global $OUTPUT, $PAGE;

        $recursing = false;
        if ($method == 'notification') {
            // Catch infinite recursion caused by debugging output during print_header.
            // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
            $backtrace = debug_backtrace();
            array_shift($backtrace);
            array_shift($backtrace);
            $recursing = is_early_init($backtrace);
        }

        $earlymethods = [
            'fatal_error' => 'early_error',
            'notification' => 'early_notification',
        ];

        // If lib/outputlib.php has been loaded, call it.
        if (!empty($PAGE) && !$recursing) {
            if (array_key_exists($method, $earlymethods)) {
                // Prevent PAGE->context warnings - exceptions might appear before we set any context.
                $PAGE->set_context(null);
            }
            $PAGE->initialise_theme_and_output();
            return call_user_func_array([$OUTPUT, $method], $arguments);
        }

        $this->initialising = true;

        // Too soon to initialise $OUTPUT, provide a couple of key methods.
        if (array_key_exists($method, $earlymethods)) {
            return call_user_func_array([self::class, $earlymethods[$method]], $arguments);
        }

        throw new coding_exception('Attempt to start output before enough information is known to initialise the theme.');
    }

    /**
     * Returns nicely formatted error message in a div box.
     *
     * @param string $message error message
     * @param ?string $moreinfourl (ignored in early errors)
     * @param ?string $link (ignored in early errors)
     * @param ?array $backtrace
     * @param ?string $debuginfo
     * @return string
     */
    public static function early_error_content($message, $moreinfourl, $link, $backtrace, $debuginfo = null) {
        global $CFG;

        $content = "<div class='alert-danger'>$message</div>";
        // Check whether debug is set.
        $debug = (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER);
        // Also check we have it set in the config file. This occurs if the method to read the config table from the
        // database fails, reading from the config table is the first database interaction we have.
        $debug = $debug || (!empty($CFG->config_php_settings['debug'])  && $CFG->config_php_settings['debug'] >= DEBUG_DEVELOPER );
        if ($debug) {
            if (!empty($debuginfo)) {
                // Remove all nasty JS.
                if (function_exists('s')) { // Function may be not available for some early errors.
                    $debuginfo = s($debuginfo);
                } else {
                    // Because weblib is not available for these early errors, we
                    // just duplicate s() code here to be safe.
                    $debuginfo = preg_replace(
                        '/&amp;#(\d+|x[0-9a-f]+);/i',
                        '&#$1;',
                        htmlspecialchars($debuginfo, ENT_QUOTES | ENT_HTML401 | ENT_SUBSTITUTE)
                    );
                }
                $debuginfo = str_replace("\n", '<br />', $debuginfo); // Keep newlines.
                $content .= '<div class="notifytiny">Debug info: ' . $debuginfo . '</div>';
            }
            if (!empty($backtrace)) {
                $content .= '<div class="notifytiny">Stack trace: ' . format_backtrace($backtrace, false) . '</div>';
            }
        }

        return $content;
    }

    /**
     * This function should only be called by this class, or from exception handlers
     *
     * @param string $message error message
     * @param string $moreinfourl (ignored in early errors)
     * @param string $link (ignored in early errors)
     * @param array $backtrace
     * @param string $debuginfo extra information for developers
     * @return ?string
     */
    public static function early_error($message, $moreinfourl, $link, $backtrace, $debuginfo = null, $errorcode = null) {
        global $CFG;

        if (CLI_SCRIPT) {
            echo "!!! $message !!!\n";
            if (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER) {
                if (!empty($debuginfo)) {
                    echo "\nDebug info: $debuginfo";
                }
                if (!empty($backtrace)) {
                    echo "\nStack trace: " . format_backtrace($backtrace, true);
                }
            }
            return;
        } else if (AJAX_SCRIPT) {
            $error = (object) [
                'error' => $message,
                'stacktrace' => null,
                'debuginfo' => null,
                'errorcode' => $errorcode,
            ];
            if (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER) {
                if (!empty($debuginfo)) {
                    $error->debuginfo = $debuginfo;
                }
                if (!empty($backtrace)) {
                    $error->stacktrace = format_backtrace($backtrace, true);
                }
            }
            @header('Content-Type: application/json; charset=utf-8');
            echo json_encode($error);
            return;
        }

        // In the name of protocol correctness, monitoring and performance
        // profiling, set the appropriate error headers for machine consumption.
        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        @header($protocol . ' 500 Internal Server Error');

        // Better disable any caching.
        @header('Content-Type: text/html; charset=utf-8');
        @header('X-UA-Compatible: IE=edge');
        @header('Cache-Control: no-store, no-cache, must-revalidate');
        @header('Cache-Control: post-check=0, pre-check=0', false);
        @header('Pragma: no-cache');
        @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
        @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        if (function_exists('get_string')) {
            $strerror = get_string('error');
        } else {
            $strerror = 'Error';
        }

        $content = self::early_error_content($message, $moreinfourl, $link, $backtrace, $debuginfo);

        return self::plain_page($strerror, $content);
    }

    /**
     * Early notification message
     *
     * @param string $message
     * @param string $classes usually notifyproblem or notifysuccess
     * @return string
     */
    public static function early_notification($message, $classes = 'notifyproblem') {
        return '<div class="' . $classes . '">' . $message . '</div>';
    }

    /**
     * Page should redirect message.
     *
     * @param string $encodedurl redirect url
     * @return string
     */
    public static function plain_redirect_message($encodedurl) {
        $message = '<div style="margin-top: 3em; margin-left:auto; margin-right:auto; text-align:center;">';
        $message .= get_string('pageshouldredirect') . '<br /><a href="';
        $message .= $encodedurl . '">' . get_string('continue') . '</a></div>';
        return self::plain_page(get_string('redirect'), $message);
    }

    /**
     * Early redirection page, used before full init of $PAGE global.
     *
     * @param string $encodedurl redirect url
     * @param string $message redirect message
     * @param int $delay time in seconds
     * @return string redirect page
     */
    public static function early_redirect_message($encodedurl, $message, $delay) {
        $meta = '<meta http-equiv="refresh" content="' . $delay . '; url=' . $encodedurl . '" />';
        $content = self::early_error_content($message, null, null, null);
        $content .= self::plain_redirect_message($encodedurl);

        return self::plain_page(get_string('redirect'), $content, $meta);
    }

    /**
     * Output basic html page.
     *
     * @param string $title page title
     * @param string $content page content
     * @param string $meta meta tag
     * @return string html page
     */
    public static function plain_page($title, $content, $meta = '') {
        global $CFG;

        if (function_exists('get_string') && function_exists('get_html_lang')) {
            $htmllang = get_html_lang();
        } else {
            $htmllang = '';
        }

        $footer = '';
        if (function_exists('get_performance_info')) { // Function may be not available for some early errors.
            if (MDL_PERF_TEST) {
                $perfinfo = get_performance_info();
                $footer = '<footer>' . $perfinfo['html'] . '</footer>';
            }
        }

        ob_start();
        include($CFG->dirroot . '/error/plainpage.php');
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(bootstrap_renderer::class, \bootstrap_renderer::class);
