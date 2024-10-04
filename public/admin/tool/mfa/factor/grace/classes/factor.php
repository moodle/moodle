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

namespace factor_grace;

use stdClass;
use tool_mfa\local\factor\object_factor_base;

/**
 * Grace period factor class.
 *
 * @package     factor_grace
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factor extends object_factor_base {

    /**
     * Grace Factor implementation.
     * This factor is a singleton, return single instance.
     *
     * @param stdClass $user the user to check against.
     * @return array
     */
    public function get_all_user_factors(stdClass $user): array {
        global $DB;

        $records = $DB->get_records('tool_mfa', ['userid' => $user->id, 'factor' => $this->name]);

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = [
            'userid' => $user->id,
            'factor' => $this->name,
            'createdfromip' => $user->lastip,
            'timecreated' => time(),
            'revoked' => 0,
        ];
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }

    /**
     * Grace Factor implementation.
     * Singleton instance, no additional filtering needed.
     *
     * @param stdClass $user object to check against.
     * @return array the array of active factors.
     */
    public function get_active_user_factors(stdClass $user): array {
        return $this->get_all_user_factors($user);
    }

    /**
     * Grace Factor implementation.
     * Factor has no input.
     *
     * {@inheritDoc}
     */
    public function has_input(): bool {
        return false;
    }

    /**
     * Grace Factor implementation.
     * Checks the user login time against their first login after MFA activation.
     *
     * @param bool $redirectable should this state call be allowed to redirect the user?
     * @return string state constant
     */
    public function get_state($redirectable = true): string {
        global $FULLME, $SESSION, $USER;
        $records = ($this->get_all_user_factors($USER));
        $record = reset($records);

        // First check if user has any other input or setup factors active.
        $factors = $this->get_affecting_factors();
        $total = 0;
        foreach ($factors as $factor) {
            $total += $factor->get_weight();
            // If we have hit 100 total, then we know it is possible to auth with the current setup.
            // Gracemode should no longer give points.
            if ($total >= 100) {
                return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
            }
        }

        $starttime = $record->timecreated;
        // If no start time is recorded, status is unknown.
        if (empty($starttime)) {
            return \tool_mfa\plugininfo\factor::STATE_UNKNOWN;
        } else {
            $duration = get_config('factor_grace', 'graceperiod');

            if (!empty($duration)) {
                if (time() > $starttime + $duration) {
                    // If gracemode would have given points, but now doesnt,
                    // Jump out of the loop and force a factor setup.
                    // We will return once there is a setup, or the user tries to leave.
                    if (get_config('factor_grace', 'forcesetup') && $redirectable) {
                        if (empty($SESSION->mfa_gracemode_recursive)) {
                            // Set a gracemode lock so any further recursive gets fall past any recursive calls.
                            $SESSION->mfa_gracemode_recursive = true;

                            $factorurls = \tool_mfa\manager::get_no_redirect_urls();
                            $cleanurl = new \moodle_url($FULLME);

                            foreach ($factorurls as $factorurl) {
                                if ($factorurl->compare($cleanurl)) {
                                    $redirectable = false;
                                }
                            }

                            // We should never redirect if we have already passed.
                            if ($redirectable && \tool_mfa\manager::get_cumulative_weight() >= 100) {
                                $redirectable = false;
                            }

                            unset($SESSION->mfa_gracemode_recursive);

                            if ($redirectable) {
                                redirect(new \moodle_url('/admin/tool/mfa/user_preferences.php'),
                                    get_string('redirectsetup', 'factor_grace'));
                            }
                        }
                    }
                    return \tool_mfa\plugininfo\factor::STATE_NEUTRAL;
                } else {
                    return \tool_mfa\plugininfo\factor::STATE_PASS;
                }
            } else {
                return \tool_mfa\plugininfo\factor::STATE_UNKNOWN;
            }
        }
    }

    /**
     * Grace Factor implementation.
     * State cannot be set. Return true.
     *
     * @param string $state the state constant to set
     * @return bool
     */
    public function set_state(string $state): bool {
        return true;
    }

    /**
     * Grace Factor implementation.
     * Add a notification on the next page.
     *
     * {@inheritDoc}
     */
    public function post_pass_state(): void {
        global $USER;
        parent::post_pass_state();

        // Ensure grace factor passed before displaying notification.
        if ($this->get_state() == \tool_mfa\plugininfo\factor::STATE_PASS
            && !\tool_mfa\manager::check_factor_pending($this->name)) {
            $url = new \moodle_url('/admin/tool/mfa/user_preferences.php');
            $link = \html_writer::link($url, get_string('preferences', 'factor_grace'));

            $records = ($this->get_all_user_factors($USER));
            $record = reset($records);
            $starttime = $record->timecreated;
            $timeremaining = ($starttime + get_config('factor_grace', 'graceperiod')) - time();
            $time = format_time($timeremaining);

            $data = ['url' => $link, 'time' => $time];

            $customwarning = get_config('factor_grace', 'customwarning');
            if (!empty($customwarning)) {
                // Clean text, then swap placeholders for time and the setup link.
                $message = preg_replace("/{timeremaining}/", $time, $customwarning);
                $message = preg_replace("/{setuplink}/", $url, $message);
                $message = clean_text($message, FORMAT_MOODLE);
            } else {
                $message = get_string('setupfactors', 'factor_grace', $data);
            }

            \core\notification::error($message);
        }
    }

    /**
     * Grace Factor implementation.
     * Gracemode should not be a valid combination with another factor.
     *
     * @param array $combination array of factors that make up the combination
     * @return bool
     */
    public function check_combination(array $combination): bool {
        // If this combination has more than 1 factor that has setup or input, not valid.
        foreach ($combination as $factor) {
            if ($factor->has_setup() || $factor->has_input()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Grace Factor implementation.
     * Gracemode can change outcome just by waiting, or based on other factors.
     *
     * @param stdClass $user
     * @return array
     */
    public function possible_states(stdClass $user): array {
        return [
            \tool_mfa\plugininfo\factor::STATE_PASS,
            \tool_mfa\plugininfo\factor::STATE_NEUTRAL,
        ];
    }

    /**
     * Grace factor implementation.
     *
     * If grace period should redirect at end, make this a no-redirect url.
     *
     * @return array
     */
    public function get_no_redirect_urls(): array {
        $redirect = get_config('factor_grace', 'forcesetup');

        // First check if user has any other input or setup factors active.
        $factors = $this->get_affecting_factors();
        $total = 0;
        foreach ($factors as $factor) {
            $total += $factor->get_weight();
            // If we have hit 100 total, then we know it is possible to auth with the current setup.
            // The setup URL should no longer be a no-redirect URL. User MUST use existing auth.
            if ($total >= 100) {
                return [];
            }
        }

        if ($redirect && $this->get_state(false) === \tool_mfa\plugininfo\factor::STATE_NEUTRAL) {
            // If the config is enabled, the user should be able to access + setup a factor using these pages.
            return [
                new \moodle_url('/admin/tool/mfa/user_preferences.php'),
                new \moodle_url('/admin/tool/mfa/action.php'),
            ];
        } else {
            return [];
        }
    }

    /**
     * Returns a list of factor objects that can affect gracemode giving points.
     *
     * Only factors that a user can setup or manually use can affect whether gracemode gives points.
     * The intest is to provide a grace period for users to go in, setup factors, phone numbers, etc.,
     * so that they are able to authenticate correctly once the grace period ends.
     *
     * @return array
     */
    public function get_all_affecting_factors(): array {
        // Check if user has any other input or setup factors active.
        $factors = \tool_mfa\plugininfo\factor::get_factors();
        $factors = array_filter($factors, function ($el) {
            return $el->has_input() || $el->has_setup();
        });
        return $factors;
    }

    /**
     * Get the factor list that is currently affecting gracemode. Active and not ignored.
     *
     * @return array
     */
    public function get_affecting_factors(): array {
        // We need to filter all active user factors against the affecting factors and ignorelist.
        // Map active to names for filtering.
        $active = \tool_mfa\plugininfo\factor::get_active_user_factor_types();
        $active = array_map(function ($el) {
            return $el->name;
        }, $active);
        $factors = $this->get_all_affecting_factors();

        $ignorelist = get_config('factor_grace', 'ignorelist');
        $ignorelist = !empty($ignorelist) ? explode(',', $ignorelist) : [];

        $factors = array_filter($factors, function ($el) use ($ignorelist, $active) {
            return !in_array($el->name, $ignorelist) && in_array($el->name, $active);
        });
        return $factors;
    }
}
