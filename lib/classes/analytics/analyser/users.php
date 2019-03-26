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
 * Users analyser (insights for users).
 *
 * @package   core
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\analytics\analyser;

defined('MOODLE_INTERNAL') || die();

/**
 * Users analyser (insights for users).
 *
 * @package   core
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class users extends \core_analytics\local\analyser\base {

    /**
     * The site users are the analysable elements returned by this analyser.
     *
     * @return \core_analytics\analysable[]
     */
    public function get_analysables() {
        global $DB, $CFG;

        $siteadmins = explode(',', $CFG->siteadmins);

        $params = ['deleted' => 0, 'confirmed' => 1, 'suspended' => 0];
        $users = $DB->get_records('user', $params, 'timecreated', 'id');

        $analysables = array();
        foreach ($users as $user) {

            if (in_array($user->id, $siteadmins) || isguestuser($user->id)) {
                // Skip admins and the guest user.
                continue;
            }
            $analysable = \core_analytics\user::instance($user->id);
            $analysables[$analysable->get_id()] = $analysable;
        }

        if (empty($analysables)) {
            $this->log[] = get_string('nousersfound');
        }

        return $analysables;
    }

    /**
     * Just one sample per analysable.
     *
     * @return bool
     */
    public static function one_sample_per_analysable() {
        return true;
    }

    /**
     * Samples origin is user table.
     *
     * @return string
     */
    public function get_samples_origin() {
        return 'user';
    }

    /**
     * Returns the analysable of a sample
     *
     * @param int $sampleid
     * @return \core_analytics\analysable
     */
    public function get_sample_analysable($sampleid) {
        return \core_analytics\user::instance($sampleid);
    }

    /**
     * This provides samples' user and context.
     *
     * @return string[]
     */
    protected function provided_sample_data() {
        return ['user', 'context'];
    }

    /**
     * Returns the context of a sample.
     *
     * @param int $sampleid
     * @return \context
     */
    public function sample_access_context($sampleid) {
        return \context_user::instance($sampleid);
    }

    /**
     * This will return just one user as we analyse users separately.
     *
     * @param \core_analytics\analysable $user
     * @return array
     */
    public function get_all_samples(\core_analytics\analysable $user) {

        $context = \context_user::instance($user->get_id());

        // Just 1 sample per analysable.
        return [
            [$user->get_id() => $user->get_id()],
            [$user->get_id() => ['user' => $user->get_user_data(), 'context' => $context]]
        ];
    }

    /**
     * Returns samples data from sample ids.
     *
     * @param int[] $sampleids
     * @return array
     */
    public function get_samples($sampleids) {
        global $DB;

        list($sql, $params) = $DB->get_in_or_equal($sampleids, SQL_PARAMS_NAMED);
        $users = $DB->get_records_select('user', "id $sql", $params);

        $userids = array_keys($users);
        $sampleids = array_combine($userids, $userids);

        $users = array_map(function($user) {
            return ['user' => $user, 'context' => \context_user::instance($user->id)];
        }, $users);

        // No related data attached.
        return [$sampleids, $users];
    }

    /**
     * Returns the description of a sample.
     *
     * @param int $sampleid
     * @param int $contextid
     * @param array $sampledata
     * @return array array(string, \renderable)
     */
    public function sample_description($sampleid, $contextid, $sampledata) {
        $description = fullname($sampledata['user']);
        return [$description, new \user_picture($sampledata['user'])];
    }

    /**
     * We need to delete associated data if a user requests his data to be deleted.
     *
     * @return bool
     */
    public function processes_user_data() {
        return true;
    }

    /**
     * Join the samples origin table with the user id table.
     *
     * @param string $sampletablealias
     * @return string
     */
    public function join_sample_user($sampletablealias) {
        return "JOIN {user} u ON u.id = {$sampletablealias}.sampleid";
    }
}
