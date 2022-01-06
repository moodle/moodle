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
 * Deprecated analyser for testing purposes.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Deprecated analyser for testing purposes.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deprecated_analyser extends \core_analytics\local\analyser\base {

    /**
     * Implementation of a deprecated method.
     *
     * It should be called by get_analysables_iterator, which triggers a debugging message.
     * @return \core_analytics\analysable[]
     */
    public function get_analysables() {
        $analysable = new \core_analytics\site();
        return [SYSCONTEXTID => $analysable];
    }

    /**
     * Samples origin is course table.
     *
     * @return string
     */
    public function get_samples_origin() {
        return 'user';
    }

    /**
     * Returns the sample analysable
     *
     * @param int $sampleid
     * @return \core_analytics\analysable
     */
    public function get_sample_analysable($sampleid) {
        return new \core_analytics\site();
    }

    /**
     * Data this analyer samples provide.
     *
     * @return string[]
     */
    protected function provided_sample_data() {
        return array('user');
    }

    /**
     * Returns the sample context.
     *
     * @param int $sampleid
     * @return \context
     */
    public function sample_access_context($sampleid) {
        return \context_system::instance();
    }

    /**
     * Returns all site courses.
     *
     * @param \core_analytics\analysable $site
     * @return array
     */
    public function get_all_samples(\core_analytics\analysable $site) {
        global $DB;

        $users = $DB->get_records('user');
        $userids = array_keys($users);
        $sampleids = array_combine($userids, $userids);

        $users = array_map(function($user) {
            return array('user' => $user);
        }, $users);

        return array($sampleids, $users);
    }

    /**
     * Return all complete samples data from sample ids.
     *
     * @param int[] $sampleids
     * @return array
     */
    public function get_samples($sampleids) {
        global $DB;

        list($userssql, $params) = $DB->get_in_or_equal($sampleids, SQL_PARAMS_NAMED);
        $users = $DB->get_records_select('user', "id {$userssql}", $params);
        $userids = array_keys($users);
        $sampleids = array_combine($userids, $userids);

        $users = array_map(function($user) {
            return array('user' => $user);
        }, $users);

        return array($sampleids, $users);
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
        $userimage = new \pix_icon('i/user', get_string('user'));
        return array($description, $userimage);
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