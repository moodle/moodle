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
 * Azure AD user sync scheduled task.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\task;

use core\task\scheduled_task;
use local_o365\feature\usersync\main;
use local_o365\utils;

defined('MOODLE_INTERNAL') || die();

/**
 * Scheduled task to sync users with Azure AD.
 */
class usersync extends scheduled_task {
    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_syncusers', 'local_o365');
    }

    /**
     * Get a stored token.
     *
     * @param string $name The token name.
     *
     * @return string|null The token, or null if empty/not found.
     */
    protected function get_token($name) {
        $token = get_config('local_o365', 'task_usersync_last' . $name);

        return (!empty($token)) ? $token : null;
    }

    /**
     * Store a token.
     *
     * @param string $name The token name.
     * @param string $value The token value.
     */
    protected function store_token($name, $value) {
        if (empty($value)) {
            $value = '';
        }
        set_config('task_usersync_last' . $name, $value, 'local_o365');
    }

    /**
     * Print debugging information using mtrace.
     *
     * @param string $msg
     */
    protected function mtrace($msg) {
        mtrace('...... ' . $msg);
    }

    /**
     * Do the job.
     */
    public function execute() {
        if (utils::is_connected() !== true) {
            $this->mtrace('Microsoft 365 not configured');

            return false;
        }

        if (main::is_enabled() !== true) {
            $this->mtrace('Azure AD cron sync disabled. Nothing to do.');

            return true;
        }
        $this->mtrace('Starting sync');
        raise_memory_limit(MEMORY_HUGE);

        $usersync = new main();

        // Do not time out when syncing users.
        @set_time_limit(0);

        $fullsyncfailed = false;

        if (main::sync_option_enabled('nodelta') === true) {
            $skiptoken = $this->get_token('skiptokenfull');
            if (!empty($skiptoken)) {
                $this->mtrace('Using skiptoken (full)');
            } else {
                $this->mtrace('No skiptoken (full) stored.');
            }

            $this->mtrace('Forcing full sync.');
            $this->mtrace('Contacting Azure AD...');
            $users = [];
            try {
                $continue = true;
                while ($continue) {
                    [$returnedusers, $skiptoken] = $usersync->get_users('default', $skiptoken);
                    $users = array_merge($users, $returnedusers);
                    $continue = (!empty($skiptoken));
                }
            } catch (\Exception $e) {
                $fullsyncfailed = true;
                $this->mtrace('Error in full usersync: ' . $e->getMessage());
                utils::debug($e->getMessage(), __METHOD__, $e);
                $this->mtrace('Resetting skip and delta tokens.');
                $skiptoken = null;
            }
            $this->mtrace('Got response from Azure AD');

            // Store skiptoken.
            if (!empty($skiptoken)) {
                $this->mtrace('Storing skiptoken (full)');
            } else {
                $this->mtrace('Clearing skiptoken (full) (none received)');
            }
            $this->store_token('skiptokenfull', $skiptoken);
        } else {
            $skiptoken = $this->get_token('skiptokendelta');
            if (!empty($skiptoken)) {
                $this->mtrace('Using skiptoken (delta)');
            } else {
                $this->mtrace('No skiptoken (delta) stored.');
            }

            $deltatoken = $this->get_token('deltatoken');
            if (!empty($deltatoken)) {
                $this->mtrace('Using deltatoken.');
            } else {
                $this->mtrace('No deltatoken stored.');
            }

            $this->mtrace('Using delta sync.');
            $this->mtrace('Contacting Azure AD...');
            $users = [];
            try {
                $continue = true;
                while ($continue) {
                    [$returnedusers, $skiptoken, $deltatoken] = $usersync->get_users_delta('default', $skiptoken, $deltatoken);
                    $users = array_merge($users, $returnedusers);
                    $continue = (empty($deltatoken) && !empty($skiptoken));
                }
            } catch (\Exception $e) {
                $this->mtrace('Error in delta usersync: ' . $e->getMessage());
                utils::debug($e->getMessage(), __METHOD__, $e);
                $this->mtrace('Resetting skip and delta tokens.');
                $skiptoken = null;
                $deltatoken = null;
            }

            $this->mtrace('Got response from Azure AD');

            // Store deltatoken.
            if (!empty($deltatoken)) {
                $this->mtrace('Storing deltatoken');
            } else {
                $this->mtrace('Clearing deltatoken (none received)');
            }
            $this->store_token('deltatoken', $deltatoken);

            // Store skiptoken.
            if (!empty($skiptoken)) {
                $this->mtrace('Storing skiptoken (delta)');
            } else {
                $this->mtrace('Clearing skiptoken (delta) (none received)');
            }
            $this->store_token('skiptokendelta', $skiptoken);
        }

        if (!empty($users)) {
            $this->mtrace(count($users) . ' users received. Syncing...');
            $this->sync_users($usersync, $users);
        } else {
            $this->mtrace('No users received to sync.');
        }

        if (main::sync_option_enabled('suspend') || main::sync_option_enabled('reenable')) {
            $lastrundate = get_config('local_o365', 'task_usersync_lastdelete');
            $rundelete = true;
            $alreadyruntoday = false;

            if (strlen($lastrundate) == 10) {
                $lastrundate = false;
            }
            if ($lastrundate && $lastrundate >= date('Ymd')) {
                $alreadyruntoday = true;
                $rundelete = false;
            }
            if (!$alreadyruntoday) {
                $suspensiontaskhour = get_config('local_o365', 'usersync_suspension_h');
                $suspensiontaskminute = get_config('local_o365', 'usersync_suspension_m');
                if (!$suspensiontaskhour) {
                    $suspensiontaskhour = 0;
                }
                if(!$suspensiontaskminute) {
                    $suspensiontaskminute = 0;
                }
                $currenthour = date('H');
                $currentminute = date('i');
                if ($currenthour > $suspensiontaskhour) {
                    set_config('task_usersync_lastdelete', date('Ymd'), 'local_o365');
                } else if (($currenthour == $suspensiontaskhour) && ($currentminute >= $suspensiontaskminute)) {
                    set_config('task_usersync_lastdelete', date('Ymd'), 'local_o365');
                } else {
                    $rundelete = false;
                }
            }

            if ($lastrundate != false) {
                if (date('Ymd') <= $lastrundate) {
                    $rundelete = false;
                    $this->mtrace('Suspend/delete users feature skipped because it was run less than 1 day ago.');
                }
            }
            if ($rundelete) {
                $this->mtrace('Start suspend/delete users feature...');
                if (main::sync_option_enabled('nodelta') !== true) {
                    // Make sure $users contains all aad users - if delta sync was used, do a full sync.
                    $skiptoken = '';
                    $users = [];

                    try {
                        $continue = true;
                        while ($continue) {
                            [$returnedusers, $skiptoken] = $usersync->get_users('default', $skiptoken);
                            $users = array_merge($users, $returnedusers);
                            $continue = (!empty($skiptoken));
                        }
                    } catch (\Exception $e) {
                        $fullsyncfailed = true;
                        $this->mtrace('Error in full usersync: ' . $e->getMessage());
                        utils::debug($e->getMessage(), __METHOD__, $e);
                        $this->mtrace('Resetting skip and delta tokens.');
                        $skiptoken = null;
                    }
                }

                if ($fullsyncfailed) {
                    $this->mtrace('Full user sync failed, skip suspending users...');
                } else {
                    if (main::sync_option_enabled('suspend')) {
                        $this->mtrace('Suspending deleted users...');
                        $usersync->suspend_users($users, main::sync_option_enabled('delete'));
                    }
                    if (main::sync_option_enabled('reenable')) {
                        $this->mtrace('Re-enabling suspended users...');
                        $usersync->reenable_suspsend_users($users, main::sync_option_enabled('disabledsync'));
                    }
                }
            }
        }

        $this->mtrace('Sync process finished.');

        return true;
    }

    /**
     * Process users in chunks of 10000 at a time.
     *
     * @param main $usersync
     * @param array $users
     */
    protected function sync_users($usersync, $users) {
        $chunk = array_chunk($users, 10000);
        foreach ($chunk as $u) {
            $this->mtrace(count($u) . ' users in chunk. Syncing...');
            $usersync->sync_users($u);
        }
    }
}
