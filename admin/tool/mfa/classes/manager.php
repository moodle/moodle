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

namespace tool_mfa;

use dml_exception;
use tool_mfa\plugininfo\factor;

/**
 * MFA management class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** @var int */
    const REDIRECT = 1;

    /** @var int */
    const NO_REDIRECT = 0;

    /** @var int */
    const REDIRECT_EXCEPTION = -1;

    /** @var int */
    const REDIR_LOOP_THRESHOLD = 5;

    /**
     * Displays a debug table with current factor information.
     *
     * @return void
     */
    public static function display_debug_notification(): void {
        global $OUTPUT, $PAGE;

        if (!get_config('tool_mfa', 'debugmode')) {
            return;
        }
        $html = $OUTPUT->heading(get_string('debugmode:heading', 'tool_mfa'), 3);

        $table = new \html_table();
        $table->head = [
            get_string('weight', 'tool_mfa'),
            get_string('factor', 'tool_mfa'),
            get_string('setup', 'tool_mfa'),
            get_string('achievedweight', 'tool_mfa'),
            get_string('status'),
        ];
        $table->attributes['class'] = 'admintable generaltable table table-bordered';
        $table->colclasses = [
            'text-right',
            '',
            '',
            'text-right',
            'text-center',
        ];
        $factors = factor::get_enabled_factors();
        $userfactors = factor::get_active_user_factor_types();
        $runningtotal = 0;
        $weighttoggle = false;

        foreach ($factors as $factor) {
            $namespace = 'factor_'.$factor->name;
            $name = get_string('pluginname', $namespace);

            // If factor is unknown, pending from here.
            if ($factor->get_state() == factor::STATE_UNKNOWN) {
                $weighttoggle = true;
            }

            // Stop adding weight if 100 achieved.
            if (!$weighttoggle) {
                $achieved = $factor->get_state() == factor::STATE_PASS ? $factor->get_weight() : 0;
                $achieved = '+'.$achieved;
                $runningtotal += $achieved;
            } else {
                $achieved = '';
            }

            // Setup.
            if ($factor->has_setup()) {
                $found = false;
                foreach ($userfactors as $userfactor) {
                    if ($userfactor->name == $factor->name) {
                        $found = true;
                    }
                }
                $setup = $found ? get_string('yes') : get_string('no');
            } else {
                $setup = get_string('na', 'tool_mfa');
            }

            // Status.
            $OUTPUT = $PAGE->get_renderer('tool_mfa');
            // If toggle has been flipped, fall to default pending badge.
            if ($weighttoggle) {
                $state = $OUTPUT->get_state_badge('');
            } else {
                $state = $OUTPUT->get_state_badge($factor->get_state());
            }

            $table->data[] = [
                $factor->get_weight(),
                $name,
                $setup,
                $achieved,
                $state,
            ];

            // If we just hit 100, flip toggle.
            if ($runningtotal >= 100) {
                $weighttoggle = true;
            }
        }

        $finalstate = self::get_status();
        $table->data[] = [
            '',
            '',
            '<b>' . get_string('overall', 'tool_mfa') . '</b>',
            self::get_cumulative_weight(),
            $OUTPUT->get_state_badge($finalstate),
        ];

        $html .= \html_writer::table($table);
        echo $html;
    }

    /**
     * Returns the total weight from all factors currently enabled for user.
     *
     * @return int
     */
    public static function get_total_weight(): int {
        $totalweight = 0;
        $factors = factor::get_active_user_factor_types();

        foreach ($factors as $factor) {
            if ($factor->get_state() == factor::STATE_PASS) {
                $totalweight += $factor->get_weight();
            }
        }
        return $totalweight;
    }

    /**
     * Checks that provided factorid exists and belongs to current user.
     *
     * @param int $factorid
     * @param object $user
     * @return bool
     * @throws \dml_exception
     */
    public static function is_factorid_valid(int $factorid, object $user): bool {
        global $DB;
        return $DB->record_exists('tool_mfa', ['userid' => $user->id, 'id' => $factorid]);
    }

    /**
     * Function to display to the user that they cannot login, then log them out.
     *
     * @return void
     */
    public static function cannot_login(): void {
        global $ME, $PAGE, $SESSION, $USER;

        // Determine page URL without triggering warnings from $PAGE.
        if (!preg_match("~(\/admin\/tool\/mfa\/auth.php)~", $ME)) {
            // If URL isn't set, we need to redir to auth.php.
            // This ensures URL and required info is correctly set.
            // Then we arrive back here.
            redirect(new \moodle_url('/admin/tool/mfa/auth.php'));
        }

        $renderer = $PAGE->get_renderer('tool_mfa');

        echo $renderer->header();
        if (get_config('tool_mfa', 'debugmode')) {
            self::display_debug_notification();
        }
        echo $renderer->not_enough_factors();
        echo $renderer->footer();
        // Emit an event for failure, then logout.
        $event = \tool_mfa\event\user_failed_mfa::user_failed_mfa_event($USER);
        $event->trigger();

        // We should set the redir flag, as this page is generated through auth.php.
        $SESSION->tool_mfa_has_been_redirected = true;
        die;
    }

    /**
     * Logout user.
     *
     * @return void
     */
    public static function mfa_logout(): void {
        $authsequence = get_enabled_auth_plugins();
        foreach ($authsequence as $authname) {
            $authplugin = get_auth_plugin($authname);
            $authplugin->logoutpage_hook();
        }
        require_logout();
    }

    /**
     * Function to get the overall status of a user's authentication.
     *
     * @return string a STATE variable from plugininfo
     */
    public static function get_status(): string {
        global $SESSION;

        // Check for any instant fail states.
        $factors = factor::get_active_user_factor_types();
        foreach ($factors as $factor) {
            $factor->load_locked_state();

            if ($factor->get_state() == factor::STATE_FAIL) {
                return factor::STATE_FAIL;
            }
        }

        $passcondition = ((isset($SESSION->tool_mfa_authenticated) && $SESSION->tool_mfa_authenticated) ||
            self::passed_enough_factors());

        // Check next factor for instant fail (fallback).
        if (factor::get_next_user_login_factor()->get_state() == factor::STATE_FAIL) {
            // We need to handle a special case here, where someone reached the fallback,
            // If they were able to modify their state on the error page, such as passing iprange,
            // We must return pass.
            if ($passcondition) {
                return factor::STATE_PASS;
            }

            return factor::STATE_FAIL;
        }

        // Now check for general passing state. If found, ensure that session var is set.
        if ($passcondition) {
            return factor::STATE_PASS;
        }

        // Else return neutral state.
        return factor::STATE_NEUTRAL;
    }

    /**
     * Function to check the overall status of a users authentication,
     * and perform any required actions.
     *
     * @param bool $shouldreload whether the function should reload (used for auth.php).
     * @return void
     */
    public static function resolve_mfa_status(bool $shouldreload = false): void {
        global $SESSION;

        $state = self::get_status();
        if ($state == factor::STATE_PASS) {
            self::set_pass_state();
            // Check if user even had to reach auth page.
            if (isset($SESSION->tool_mfa_has_been_redirected)) {
                if (empty($SESSION->wantsurl)) {
                    $wantsurl = '/';
                } else {
                    $wantsurl = $SESSION->wantsurl;
                }
                unset($SESSION->wantsurl);
                redirect(new \moodle_url($wantsurl));
            } else {
                // Don't touch anything, let user be on their way.
                return;
            }
        } else if ($state == factor::STATE_FAIL) {
            self::cannot_login();
        } else if ($shouldreload) {
            // Set a session variable to track whether user is where they want to be.
            $SESSION->tool_mfa_has_been_redirected = true;
            $authurl = new \moodle_url('/admin/tool/mfa/auth.php');
            redirect($authurl);
        }
    }

    /**
     * Checks whether user has passed enough factors to be allowed in.
     *
     * @return bool true if user has passed enough factors.
     */
    public static function passed_enough_factors(): bool {

        // Check for any instant fail states.
        $factors = factor::get_active_user_factor_types();
        foreach ($factors as $factor) {
            if ($factor->get_state() == factor::STATE_FAIL) {
                self::mfa_logout();
            }
        }

        $totalweight = self::get_cumulative_weight();
        if ($totalweight >= 100) {
            return true;
        }

        return false;
    }

    /**
     * Sets the session variable for pass_state, if not already set.
     *
     * @return void
     */
    public static function set_pass_state(): void {
        global $DB, $SESSION, $USER;
        if (!isset($SESSION->tool_mfa_authenticated)) {
            $SESSION->tool_mfa_authenticated = true;
            $event = \tool_mfa\event\user_passed_mfa::user_passed_mfa_event($USER);
            $event->trigger();

            // Allow plugins to callback as soon possible after user has passed MFA.
            $hook = new \tool_mfa\hook\after_user_passed_mfa();
            \core\di::get(\core\hook\manager::class)->dispatch($hook);

            // Add/update record in DB for users last mfa auth.
            self::update_pass_time();

            // Unset session vars during mfa auth.
            unset($SESSION->mfa_redir_referer);
            unset($SESSION->mfa_redir_count);

            // Unset user preferences during mfa auth.
            unset_user_preference('mfa_sleep_duration', $USER);

            try {
                // Clear locked user factors, they may now reauth with anything.
                @$DB->set_field('tool_mfa', 'lockcounter', 0, ['userid' => $USER->id]);
            // @codingStandardsIgnoreStart
            } catch (\Exception $e) {
                // This occurs when upgrade.php hasn't been run. Nothing to do here.
                // Coding standards ignored, they break on empty catches.
            }
            // @codingStandardsIgnoreEnd

            // Fire post pass state factor actions.
            $factors = factor::get_active_user_factor_types();
            foreach ($factors as $factor) {
                $factor->post_pass_state();
                // Also set the states for this session to neutral if they were locked.
                if ($factor->get_state() == factor::STATE_LOCKED) {
                    $factor->set_state(factor::STATE_NEUTRAL);
                }
            }

            // Output notifications if any factors were reset for this user.
            $enabledfactors = factor::get_enabled_factors();
            foreach ($enabledfactors as $factor) {
                $pref = 'tool_mfa_reset_' . $factor->name;
                $factorpref = get_user_preferences($pref, false);
                if ($factorpref) {
                    $url = new \moodle_url('/admin/tool/mfa/user_preferences.php');
                    $link = \html_writer::link($url, get_string('preferenceslink', 'tool_mfa'));
                    $data = ['factor' => $factor->get_display_name(), 'url' => $link];
                    \core\notification::warning(get_string('factorreset', 'tool_mfa', $data));
                    unset_user_preference($pref);
                }
            }

            // Also check for a global reset.
            // TODO: Delete this in a few months, the reset all preference is no longer set.
            $allfactor = get_user_preferences('tool_mfa_reset_all', false);
            if ($allfactor) {
                $url = new \moodle_url('/admin/tool/mfa/user_preferences.php');
                $link = \html_writer::link($url, get_string('preferenceslink', 'tool_mfa'));
                \core\notification::warning(get_string('factorresetall', 'tool_mfa', $link));
                unset_user_preference('tool_mfa_reset_all');
            }
        }
    }

    /**
     * Inserts or updates user's last MFA pass time in DB.
     * This should only be called from set_pass_state.
     *
     * @return void
     */
    private static function update_pass_time(): void {
        global $DB, $USER;

        $exists = $DB->record_exists('tool_mfa_auth', ['userid' => $USER->id]);

        if ($exists) {
            $DB->set_field('tool_mfa_auth', 'lastverified', time(), ['userid' => $USER->id]);
        } else {
            $DB->insert_record('tool_mfa_auth', ['userid' => $USER->id, 'lastverified' => time()]);
        }
    }

    /**
     * Checks whether the user should be redirected from the provided url.
     *
     * @param string|\moodle_url $url
     * @param bool|null $preventredirect
     * @return int
     */
    public static function should_require_mfa(string|\moodle_url $url, bool|null $preventredirect): int {
        global $CFG, $USER, $SESSION;

        // If no cookies then no session so cannot do MFA.
        // Unit testing based on defines is not viable.
        if (NO_MOODLE_COOKIES && !PHPUNIT_TEST) {
            return self::NO_REDIRECT;
        }

        // Remove all params before comparison.
        $url->remove_all_params();

        // Checks for upgrades pending.
        if (is_siteadmin()) {
            // We should only allow an upgrade from the frontend to complete.
            // After that is completed, only the settings shouldn't redirect.
            // Everything else should be safe to enforce MFA.
            if (moodle_needs_upgrading()) {
                return self::NO_REDIRECT;
            }
            // An upgrade isn't complete if there are settings that must be saved.
            $upgradesettings = new \moodle_url('/admin/upgradesettings.php');
            if ($url->compare($upgradesettings, URL_MATCH_BASE)) {
                return self::NO_REDIRECT;
            }
        }

        // Dont redirect logo images from pluginfile.php (for example: logo in header).
        $logourl = new \moodle_url('/pluginfile.php/1/core_admin/logocompact/');
        if ($url->compare($logourl)) {
            return self::NO_REDIRECT;
        }

        // Admin not setup.
        if (!empty($CFG->adminsetuppending)) {
            return self::NO_REDIRECT;
        }

        // Initial installation.
        // We get this for free from get_plugins_with_function.

        // Upgrade check.
        // We get this for free from get_plugins_with_function.

        // Honor prevent_redirect.
        if ($preventredirect) {
            return self::NO_REDIRECT;
        }

        // User not properly setup.
        if (user_not_fully_set_up($USER)) {
            return self::NO_REDIRECT;
        }

        // Enrolment.
        $enrol = new \moodle_url('/enrol/index.php');
        if ($enrol->compare($url, URL_MATCH_BASE)) {
            return self::NO_REDIRECT;
        }

        // Guest access.
        if (isguestuser()) {
            return self::NO_REDIRECT;
        }

        // Forced password changes.
        if (get_user_preferences('auth_forcepasswordchange')) {
            return self::NO_REDIRECT;
        }

        // Login as.
        if (\core\session\manager::is_loggedinas()) {
            return self::NO_REDIRECT;
        }

        // Site policy.
        if (isset($USER->policyagreed) && !$USER->policyagreed) {
            $manager = new \core_privacy\local\sitepolicy\manager();
            $policyurl = $manager->get_redirect_url(false);
            if (!empty($policyurl) && $url->compare($policyurl, URL_MATCH_BASE)) {
                return self::NO_REDIRECT;
            }
        }

        // WS/AJAX check.
        if (WS_SERVER || AJAX_SCRIPT) {
            if (isset($SESSION->mfa_pending) && !empty($SESSION->mfa_pending)) {
                // Allow AJAX and WS, but never from auth.php.
                return self::NO_REDIRECT;
            }
            return self::REDIRECT_EXCEPTION;
        }

        // Check factor defined safe urls.
        $factorurls = self::get_no_redirect_urls();
        foreach ($factorurls as $factorurl) {
            if ($factorurl->compare($url)) {
                return self::NO_REDIRECT;
            }
        }

        // Circular checks.
        $authurl = new \moodle_url('/admin/tool/mfa/auth.php');
        $authlocal = $authurl->out_as_local_url();
        if (isset($SESSION->mfa_redir_referer)
            && $SESSION->mfa_redir_referer != $authlocal) {
            if ($SESSION->mfa_redir_referer == get_local_referer(true)) {
                // Possible redirect loop.
                if (!isset($SESSION->mfa_redir_count)) {
                    $SESSION->mfa_redir_count = 1;
                } else {
                    $SESSION->mfa_redir_count++;
                }
                if ($SESSION->mfa_redir_count > self::REDIR_LOOP_THRESHOLD) {
                    return self::REDIRECT_EXCEPTION;
                }
            } else {
                // If not a match, reset counter.
                $SESSION->mfa_redir_count = 0;
            }
        }
        // Set referer after checks.
        $SESSION->mfa_redir_referer = get_local_referer(true);

        // Don't redirect if already on auth.php.
        if ($url->compare($authurl, URL_MATCH_BASE)) {
            return self::NO_REDIRECT;
        }

        return self::REDIRECT;
    }

    /**
     * Clears the redirect counter for infinite redirect loops. Called from auth.php when a valid load is resolved.
     *
     * @return void
     */
    public static function clear_redirect_counter(): void {
        global $SESSION;

        unset($SESSION->mfa_redir_referer);
        unset($SESSION->mfa_redir_count);
    }

    /**
     * Gets all defined factor urls that should not redirect.
     *
     * @return array
     */
    public static function get_no_redirect_urls(): array {
        $factors = factor::get_factors();
        $urls = [
            new \moodle_url('/login/logout.php'),
            new \moodle_url('/admin/tool/mfa/guide.php'),
        ];
        foreach ($factors as $factor) {
            $urls = array_merge($urls, $factor->get_no_redirect_urls());
        }

        // Allow forced redirection exclusions.
        if ($exclusions = get_config('tool_mfa', 'redir_exclusions')) {
            foreach (explode("\n", $exclusions) as $exclusion) {
                $urls[] = new \moodle_url($exclusion);
            }
        }

        return $urls;
    }

    /**
     * Sleeps for an increasing period of time.
     *
     * @return void
     */
    public static function sleep_timer(): void {
        global $USER;

        $duration = get_user_preferences('mfa_sleep_duration', null, $USER);
        if (!empty($duration)) {
            // Double current time.
            $duration *= 2;
            $duration = min(2, $duration);
        } else {
            // No duration set.
            $duration = 0.05;
        }
        set_user_preference('mfa_sleep_duration', $duration, $USER);
        sleep((int)$duration);
    }

    /**
     * If MFA Plugin is ready check tool_mfa_authenticated USER property and
     * start MFA authentication if it's not set or false.
     *
     * @param mixed $courseorid
     * @param mixed $autologinguest
     * @param mixed $cm
     * @param mixed $setwantsurltome
     * @param mixed $preventredirect
     * @return void
     */
    public static function require_auth($courseorid = null, $autologinguest = null, $cm = null,
    $setwantsurltome = null, $preventredirect = null): void {
        global $PAGE, $SESSION, $FULLME;

        // Guest user should never interact with MFA,
        // And $SESSION->tool_mfa_authenticated should never be set in a guest session.
        if (isguestuser()) {
            return;
        }

        if (!self::is_ready()) {
            // Set session var so if MFA becomes ready, you dont get locked from session.
            $SESSION->tool_mfa_authenticated = true;
            return;
        }

        if (empty($SESSION->tool_mfa_authenticated) || !$SESSION->tool_mfa_authenticated) {
            if ($PAGE->has_set_url()) {
                $cleanurl = $PAGE->url;
            } else {
                // Use $FULLME instead.
                $cleanurl = new \moodle_url($FULLME);
            }
            $authurl = new \moodle_url('/admin/tool/mfa/auth.php');

            $redir = self::should_require_mfa($cleanurl, $preventredirect);

            if ($redir == self::NO_REDIRECT && !$cleanurl->compare($authurl, URL_MATCH_BASE)) {
                // A non-MFA page that should take precedence.
                // This check is for any pages, such as site policy, that must occur before MFA.
                // This check allows AJAX and WS requests to fire on these pages without throwing an exception.
                $SESSION->mfa_pending = true;
            }

            if ($redir == self::REDIRECT) {
                if (empty($SESSION->wantsurl)) {
                    !empty($setwantsurltome)
                        ? $SESSION->wantsurl = qualified_me()
                        : $SESSION->wantsurl = new \moodle_url('/');

                    $SESSION->tool_mfa_setwantsurl = true;
                }
                // Remove pending status.
                // We must now auth with MFA, now that pending statuses are resolved.
                unset($SESSION->mfa_pending);

                // Call resolve_status to instantly pass if no redirect is required.
                self::resolve_mfa_status(true);
            } else if ($redir == self::REDIRECT_EXCEPTION) {
                if (!empty($SESSION->mfa_redir_referer)) {
                    throw new \moodle_exception('redirecterrordetected', 'tool_mfa',
                        $SESSION->mfa_redir_referer, $SESSION->mfa_redir_referer);
                } else {
                    throw new \moodle_exception('redirecterrordetected', 'error');
                }
            }
        }
    }

    /**
     * Sets config variable for given factor.
     *
     * @param array $data
     * @param string $factor
     *
     * @return bool true or exception
     * @throws dml_exception
     */
    public static function set_factor_config(array $data, string $factor): bool|dml_exception {
        $factorconf = get_config($factor);
        foreach ($data as $key => $newvalue) {
            if (empty($factorconf->$key)) {
                add_to_config_log($key, null, $newvalue, $factor);
                set_config($key, $newvalue, $factor);
            } else if ($factorconf->$key != $newvalue) {
                add_to_config_log($key, $factorconf->$key, $newvalue, $factor);
                set_config($key, $newvalue, $factor);
            }
        }
        return true;
    }

    /**
     * Checks if MFA Plugin is enabled and has enabled factor.
     * If plugin is disabled or there is no enabled factors,
     * it means there is nothing to do from user side.
     * Thus, login flow shouldn't be extended with MFA.
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function is_ready(): bool {
        global $CFG, $USER;

        if (!empty($CFG->upgraderunning)) {
            return false;
        }

        $pluginenabled = get_config('tool_mfa', 'enabled');
        if (empty($pluginenabled)) {
            return false;
        }

        // Check if user can interact with MFA.
        $usercontext = \context_user::instance($USER->id);
        if (!has_capability('tool/mfa:mfaaccess', $usercontext)) {
            return false;
        }

        $enabledfactors = factor::get_enabled_factors();
        if (count($enabledfactors) == 0) {
            return false;
        }

        return true;
    }

    /**
     * Performs factor actions for given factor.
     * Change factor order and enable/disable.
     *
     * @param string $factorname
     * @param string $action
     *
     * @return void
     * @throws dml_exception
     */
    public static function do_factor_action(string $factorname, string $action): void {
        $order = explode(',', get_config('tool_mfa', 'factor_order'));
        $key = array_search($factorname, $order);

        switch ($action) {
            case 'up':
                if ($key >= 1) {
                    $fsave = $order[$key];
                    $order[$key] = $order[$key - 1];
                    $order[$key - 1] = $fsave;
                }
                break;

            case 'down':
                if ($key < (count($order) - 1)) {
                    $fsave = $order[$key];
                    $order[$key] = $order[$key + 1];
                    $order[$key + 1] = $fsave;
                }
                break;

            case 'enable':
                if (!$key) {
                    $order[] = $factorname;
                }
                break;

            case 'disable':
                if ($key) {
                    unset($order[$key]);
                }
                break;

            default:
                break;
        }
        self::set_factor_config(['factor_order' => implode(',', $order)], 'tool_mfa');
    }

    /**
     * Checks if a factor that can make a user pass can be setup.
     * It checks if a user will always pass regardless,
     * then checks if there are factors that can be setup to let a user pass.
     *
     * @return bool
     */
    public static function possible_factor_setup(): bool {
        global $USER;

        // Get all active factors.
        $factors = factor::get_enabled_factors();

        // Check if there are enough factors that a user can ONLY pass, if so, don't display the menu.
        $weight = 0;
        foreach ($factors as $factor) {
            $states = $factor->possible_states($USER);
            if (count($states) == 1 && reset($states) == factor::STATE_PASS) {
                $weight += $factor->get_weight();
                if ($weight >= 100) {
                    return false;
                }
            }
        }

        // Now if there is a factor that can be setup, that may return a pass state for the user, display menu.
        foreach ($factors as $factor) {
            if ($factor->has_setup()) {
                if (in_array(factor::STATE_PASS, $factor->possible_states($USER))) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Gets current user weight, up until first unknown factor.
     *
     * @return int $totalweight Total weight of all factors.
     */
    public static function get_cumulative_weight(): int {
        $factors = factor::get_active_user_factor_types();
        // Factor order is important here, so sort the factors by state.
        $sortedfactors = factor::sort_factors_by_state($factors, factor::STATE_PASS);
        $totalweight = 0;
        foreach ($sortedfactors as $factor) {
            if ($factor->get_state() == factor::STATE_PASS) {
                $totalweight += $factor->get_weight();
                // If over 100, break. Don't care about >100.
                if ($totalweight >= 100) {
                    break;
                }
            } else if ($factor->get_state() == factor::STATE_UNKNOWN) {
                break;
            }
        }
        return $totalweight;
    }

    /**
     * Checks whether the factor was actually used in the login process.
     *
     * @param string $factorname the name of the factor.
     * @return bool true if factor is pending.
     */
    public static function check_factor_pending(string $factorname): bool {
        $factors = factor::get_active_user_factor_types();
        // Setup vars.
        $pending = [];
        $totalweight = 0;
        $weighttoggle = false;

        foreach ($factors as $factor) {
            // If toggle is reached, put in pending and continue.
            if ($weighttoggle) {
                $pending[] = $factor->name;
                continue;
            }

            if ($factor->get_state() == factor::STATE_PASS) {
                $totalweight += $factor->get_weight();
                if ($totalweight >= 100) {
                    $weighttoggle = true;
                }
            }
        }

        // Check whether factor falls into pending category.
        return in_array($factorname, $pending);
    }
}
