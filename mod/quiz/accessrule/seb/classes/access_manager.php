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
 * Manage the access to the quiz.
 *
 * @package    quizaccess_seb
 * @author     Tim Hunt
 * @author     Luca Bösch <luca.boesch@bfh.ch>
 * @author     Andrew Madden <andrewmadden@catalyst-au.net>
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2019 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb;

use context_module;
use quiz;

defined('MOODLE_INTERNAL') || die();

/**
 * Manage the access to the quiz.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class access_manager {

    /** Header sent by Safe Exam Browser containing the Config Key hash. */
    private const CONFIG_KEY_HEADER = 'HTTP_X_SAFEEXAMBROWSER_CONFIGKEYHASH';

    /** Header sent by Safe Exam Browser containing the Browser Exam Key hash. */
    private const BROWSER_EXAM_KEY_HEADER = 'HTTP_X_SAFEEXAMBROWSER_REQUESTHASH';

    /** @var quiz $quiz A quiz object containing all information pertaining to current quiz. */
    private $quiz;

    /** @var quiz_settings $quizsettings A quiz settings persistent object containing plugin settings */
    private $quizsettings;

    /** @var context_module $context Context of this quiz activity. */
    private $context;

    /** @var string|null $validconfigkey Expected valid SEB config key.
     */
    private $validconfigkey = null;

    /**
     * The access_manager constructor.
     *
     * @param quiz $quiz The details of the quiz.
     */
    public function __construct(quiz $quiz) {
        $this->quiz = $quiz;
        $this->context = context_module::instance($quiz->get_cmid());
        $this->quizsettings = quiz_settings::get_by_quiz_id($quiz->get_quizid());
        $this->validconfigkey = quiz_settings::get_config_key_by_quiz_id($quiz->get_quizid());
    }

    /**
     * Check if the browser exam key hash in header matches one of the listed browser exam keys from quiz settings.
     *
     * @return bool True if header key matches one of the saved keys.
     */
    public function validate_browser_exam_keys() : bool {
        // If browser exam keys are entered in settings, check they match the header.
        $browserexamkeys = $this->quizsettings->get('allowedbrowserexamkeys');
        if (empty($browserexamkeys)) {
            return true; // If no browser exam keys, no check required.
        }

        // If the Browser Exam Key header isn't present, prevent access.
        if (is_null($this->get_received_browser_exam_key())) {
            return false;
        }

        return $this->check_browser_exam_keys($browserexamkeys, $this->get_received_browser_exam_key());
    }

    /**
     * Check if the config key hash in header matches quiz settings.
     *
     * @return bool True if header key matches saved key.
     */
    public function validate_config_key() : bool {
        // If using client config, or with no requirement, then no check required.
        $requiredtype = $this->get_seb_use_type();
        if ($requiredtype == settings_provider::USE_SEB_NO
                || $requiredtype == settings_provider::USE_SEB_CLIENT_CONFIG) {
            return true;
        }

        if (empty($this->validconfigkey)) {
            return false; // No config key has been saved.
        }

        // If the Config Key header isn't present, prevent access.
        if (is_null($this->get_received_config_key())) {
            return false;
        }

        return $this->check_key($this->validconfigkey, $this->get_received_config_key());
    }

    /**
     * Check if Safe Exam Browser is required to access quiz.
     * If quizsettings do not exist, then there is no requirement for using SEB.
     *
     * @return bool If required.
     */
    public function seb_required() : bool {
        if (!$this->quizsettings) {
            return false;
        } else {
            return $this->get_seb_use_type() != settings_provider::USE_SEB_NO;
        }
    }

    /**
     * This is the basic check for the Safe Exam Browser previously used in the quizaccess_safebrowser plugin that
     * managed basic Moodle interactions with SEB.
     *
     * @return bool
     */
    public function validate_basic_header() : bool {
        if ($this->get_seb_use_type() == settings_provider::USE_SEB_CLIENT_CONFIG) {
            return $this->is_using_seb();
        }
        return true;
    }

    /**
     * Check if using Safe Exam Browser.
     *
     * @return bool
     */
    public function is_using_seb() : bool {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            return strpos($_SERVER['HTTP_USER_AGENT'], 'SEB') !== false;
        }

        return false;
    }

    /**
     * Check if user has any capability to bypass the Safe Exam Browser requirement.
     *
     * @return bool True if user can bypass check.
     */
    public function can_bypass_seb() : bool {
        return has_capability('quizaccess/seb:bypassseb', $this->context);
    }

    /**
     * Return the full URL that was used to request the current page, which is
     * what we need for verifying the X-SafeExamBrowser-RequestHash header.
     */
    private function get_this_page_url() : string {
        global $CFG, $FULLME;
        // If $FULLME not set fall back to wwwroot.
        if ($FULLME == null) {
            return $CFG->wwwroot;
        }
        return $FULLME;
    }

    /**
     * Return expected SEB config key.
     *
     * @return string|null
     */
    public function get_valid_config_key() : ?string {
        return $this->validconfigkey;
    }

    /**
     * Getter for the quiz object.
     *
     * @return quiz
     */
    public function get_quiz() : quiz {
        return $this->quiz;
    }

    /**
     * Check the hash from the request header against the permitted browser exam keys.
     *
     * @param array $keys Allowed browser exam keys.
     * @param string $header The value of the X-SafeExamBrowser-RequestHash to check.
     * @return bool True if the hash matches.
     */
    private function check_browser_exam_keys(array $keys, string $header) : bool {
        foreach ($keys as $key) {
            if ($this->check_key($key, $header)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check the hash from the request header against a single permitted key.
     *
     * @param string $key an allowed key.
     * @param string $header the value of the X-SafeExamBrowser-RequestHash or X-SafeExamBrowser-ConfigKeyHash to check.
     * @return bool true if the hash matches.
     */
    private function check_key($key, $header) : bool {
        return hash('sha256', $this->get_this_page_url() . $key) === $header;
    }

    /**
     * Returns Safe Exam Browser Config Key hash.
     *
     * @return string|null
     */
    public function get_received_config_key() {
        if (isset($_SERVER[self::CONFIG_KEY_HEADER])) {
            return trim($_SERVER[self::CONFIG_KEY_HEADER]);
        }

        return null;
    }

    /**
     * Returns the Browser Exam Key hash.
     *
     * @return string|null
     */
    public function get_received_browser_exam_key() {
        if (isset($_SERVER[self::BROWSER_EXAM_KEY_HEADER])) {
            return trim($_SERVER[self::BROWSER_EXAM_KEY_HEADER]);
        }

        return null;
    }

    /**
     * Get type of SEB usage for the quiz.
     *
     * @return int
     */
    public function get_seb_use_type() : int {
        if (empty($this->quizsettings)) {
            return settings_provider::USE_SEB_NO;
        } else {
            return $this->quizsettings->get('requiresafeexambrowser');
        }
    }

    /**
     * Should validate basic header?
     *
     * @return bool
     */
    public function should_validate_basic_header() : bool {
        return in_array($this->get_seb_use_type(), [
            settings_provider::USE_SEB_CLIENT_CONFIG,
        ]);
    }

    /**
     * Should validate SEB config key?
     * @return bool
     */
    public function should_validate_config_key() : bool {
        return in_array($this->get_seb_use_type(), [
            settings_provider::USE_SEB_CONFIG_MANUALLY,
            settings_provider::USE_SEB_TEMPLATE,
            settings_provider::USE_SEB_UPLOAD_CONFIG,
        ]);
    }

    /**
     * Should validate browser exam key?
     *
     * @return bool
     */
    public function should_validate_browser_exam_key() : bool {
        return in_array($this->get_seb_use_type(), [
            settings_provider::USE_SEB_UPLOAD_CONFIG,
            settings_provider::USE_SEB_CLIENT_CONFIG,
        ]);
    }
}
