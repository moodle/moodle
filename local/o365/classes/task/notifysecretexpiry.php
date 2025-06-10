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
 * Notify secret expiry task.
 *
 * @package local_o365
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\task;

use core\task\scheduled_task;
use core_user;
use Exception;
use local_o365\utils;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/oidc/lib.php');

class notifysecretexpiry extends scheduled_task {
    /**
     * Return a descriptive name of the task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_notifysecretexpiry', 'local_o365');
    }

    /**
     * Run the task to check on the expiry date of the secret, and send notiifation if needed.
     *
     * @return bool
     */
    public function execute() {
        if (utils::is_connected() !== true) {
            return false;
        }

        try {
            $graphclient = utils::get_api();
        } catch (Exception $e) {
            utils::debug('Exception: ' . $e->getMessage(), __METHOD__, $e);
            mtrace('Failed to get Graph API client');
            return false;
        }

        $authenticationmethod = get_config('auth_oidc', 'clientauthmethod');
        if ($authenticationmethod != AUTH_OIDC_AUTH_METHOD_SECRET) {
            // Currently only support client secret authentication method.
            return false;
        }

        $appid = get_config('auth_oidc', 'clientid');
        $appsecret = get_config('auth_oidc', 'clientsecret');
        try {
            $appcredentials = $graphclient->get_app_credentials($appid);
        } catch (Exception $e) {
            utils::debug('Exception: ' . $e->getMessage(), __METHOD__, $e);
            mtrace ('Failed to get secrets');
            $this->notify_invalid_secret();
            return false;
        }

        $fourweeksinseconds = 60 * 60 * 24 * 7 * 4;

        if (isset($appcredentials['value'])) {
            if (isset($appcredentials['value'][0])) {
                if (isset($appcredentials['value'][0]['passwordCredentials'])) {
                    $secrets = $appcredentials['value'][0]['passwordCredentials'];
                    mtrace('Found ' . count($secrets) . ' secrets.');
                    $foundmatchingsecret = false;
                    foreach ($secrets as $secret) {
                        if (isset($secret['hint'])) {
                            if (substr($appsecret, 0, 3) == $secret['hint']) {
                                $foundmatchingsecret = true;
                                mtrace('Found the secret used for the integration');
                                if (isset($secret['endDateTime'])) {
                                    mtrace('... The secret expires at ' . $secret['endDateTime']);
                                    $endtime = strtotime($secret['endDateTime']);
                                    if ($endtime < time()) {
                                        // Secret already expired, notify site admin.
                                        $this->notify_secret_expired();
                                    } else if ($endtime - $fourweeksinseconds < time()) {
                                        // Secret to be expired in less than 4 weeks, notify site admin.
                                        mtrace('... Found secret that will expire soon.');
                                        $this->notify_secret_almost_expired($endtime);
                                    } else {
                                        // Nothing to do.
                                        mtrace('... Secret will expire well in the future.');
                                    }
                                } else {
                                    // This should never happen.
                                    mtrace('Secret does not have expiry date');
                                    $this->notify_invalid_secret();
                                }
                                mtrace('Skip processing other secrets');
                                break;
                            }
                        } else {
                            // This should never happen.
                            mtrace('Secret does not provide hint');
                        }
                    }

                    if (!$foundmatchingsecret) {
                        // No matching secret has been found.
                        // This should only happen in very rare cases,
                        // e.g. secret has been deleted, but existing token is still working.
                        mtrace('Secret used in the integration has not been found');
                        $this->notify_invalid_secret();
                    }
                }
            }
        }

        return true;
    }

    /**
     * Notify site admin about secret already expired.
     *
     * @return void
     */
    private function notify_secret_expired() {
        $adminuser = get_admin();
        $supportuser = core_user::get_support_user();
        $subject = get_string('notification_subject_secret_expired', 'local_o365');
        $message = get_string('notification_content_secret_expired', 'local_o365');

        email_to_user($adminuser, $supportuser, $subject, $message);
    }

    /**
     * Notify site admin about secret to be expired soon.
     *
     * @param int $endtime
     * @return void
     */
    private function notify_secret_almost_expired(int $endtime) {
        $adminuser = get_admin();
        $supportuser = core_user::get_support_user();

        // Calculate in how many days the secret will expire, and form duration string.
        $days = abs($endtime - time()) / 60 / 60 / 24;
        if ($days < 1) {
            $daysstring = get_string('notification_days_less_than_one_day', 'local_o365');
        } else if (intval($days) == 1) {
            $daysstring = get_string('notification_days_one_day', 'local_o365');
        } else {
            $daysstring = get_string('notification_days_days', 'local_o365', intval($days));
        }
        $subject = get_string('notification_subject_secret_almost_expired', 'local_o365');
        $message = get_string('notification_content_secret_almost_expired', 'local_o365', $daysstring);

        email_to_user($adminuser, $supportuser, $subject, $message);
    }

    /**
     * Notify site admin about invalid secret.
     *
     * @return void
     */
    private function notify_invalid_secret() {
        $adminuser = get_admin();
        $supportuser = core_user::get_support_user();
        $subject = get_string('notification_subject_invalid_secret', 'local_o365');
        $message = get_string('notification_content_invalid_secret', 'local_o365');

        email_to_user($adminuser, $supportuser, $subject, $message);
    }
}
