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

namespace enrol_lti\local\ltiadvantage\repository;

/**
 * The legacy_consumer_repository class, instances of which are responsible for querying LTI 1.1/2.0 consumer info.
 *
 * @package enrol_lti
 * @copyright 2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class legacy_consumer_repository {

    /**
     * Get a list of all shared secrets which a given LTI 1.1/2.0 consumer is associated with.
     *
     * A single consumer key may be used across several tool definitions, with different secrets, thus permitting a
     * one:many relationship between consumer and secret.
     * @param string $consumerkey the key identifying the consumer.
     * @return string[] an array of secrets corresponding to the consumer key.
     */
    public function get_consumer_secrets(string $consumerkey): array {
        global $DB;
        $sql = "SELECT t.id, t.secret
                  FROM {enrol_lti_lti2_consumer} c
                  JOIN {enrol_lti_tool_consumer_map} cm
                    ON (c.id = cm.consumerid)
                  JOIN {enrol_lti_tools} t
                    ON (t.id = cm.toolid)
                 WHERE c.consumerkey256 = :consumerkey";
        return array_unique(array_column($DB->get_records_sql($sql, ['consumerkey' => $consumerkey]), 'secret'));
    }
}
