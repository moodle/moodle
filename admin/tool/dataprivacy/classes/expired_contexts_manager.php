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
 * Expired contexts manager.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

use core_privacy\manager;
use tool_dataprivacy\expired_context;

defined('MOODLE_INTERNAL') || die();

/**
 * Expired contexts manager.
 *
 * @copyright  2018 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class expired_contexts_manager {

    /**
     * Number of deleted contexts for each scheduled task run.
     */
    const DELETE_LIMIT = 200;

    /**
     * Returns the list of expired context instances.
     *
     * @return \stdClass[]
     */
    abstract protected function get_expired_contexts();

    /**
     * Specify with context levels this expired contexts manager is deleting.
     *
     * @return int[]
     */
    abstract protected function get_context_levels();

    /**
     * Flag expired contexts as expired.
     *
     * @return int The number of contexts flagged as expired.
     */
    public function flag_expired() {

        if (!$this->check_requirements()) {
            return 0;
        }

        $contexts = $this->get_expired_contexts();
        foreach ($contexts as $context) {
            api::create_expired_context($context->id);
        }

        return count($contexts);
    }

    /**
     * Deletes the expired contexts.
     *
     * @return int The number of deleted contexts.
     */
    public function delete() {

        $numprocessed = 0;

        if (!$this->check_requirements()) {
            return $numprocessed;
        }

        $privacymanager = new manager();
        $privacymanager->set_observer(new \tool_dataprivacy\manager_observer());

        foreach ($this->get_context_levels() as $level) {

            $expiredcontexts = expired_context::get_records_by_contextlevel($level, expired_context::STATUS_APPROVED);

            foreach ($expiredcontexts as $expiredctx) {

                if (!$this->delete_expired_context($privacymanager, $expiredctx)) {
                    continue;
                }

                $numprocessed += 1;
                if ($numprocessed == self::DELETE_LIMIT) {
                    // Close the recordset.
                    $expiredcontexts->close();
                    break 2;
                }
            }
        }

        return $numprocessed;
    }

    /**
     * Deletes user data from the provided context.
     *
     * @param manager $privacymanager
     * @param expired_context $expiredctx
     * @return \context|false
     */
    protected function delete_expired_context(manager $privacymanager, expired_context $expiredctx) {

        $context = \context::instance_by_id($expiredctx->get('contextid'), IGNORE_MISSING);
        if (!$context) {
            api::delete_expired_context($expiredctx->get('contextid'));
            return false;
        }

        if (!PHPUNIT_TEST) {
            mtrace('Deleting context ' . $context->id . ' - ' .
                shorten_text($context->get_context_name(true, true)));
        }

        $privacymanager->delete_data_for_all_users_in_context($context);
        api::set_expired_context_status($expiredctx, expired_context::STATUS_CLEANED);

        return $context;
    }

    /**
     * Check that the requirements to start deleting contexts are satisified.
     *
     * @return bool
     */
    protected function check_requirements() {
        api::check_can_manage_data_registry(\context_system::instance()->id);

        if (!data_registry::defaults_set()) {
            return false;
        }
        return true;
    }
}
