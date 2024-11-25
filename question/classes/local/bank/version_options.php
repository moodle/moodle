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

namespace core_question\local\bank;

/**
 * Helper methods for question version options.
 *
 * @package    core_question
 * @copyright  2024 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 */
class version_options {

    /**
     * Get the version options for display in dropdown menu
     * @param int $questionid
     * @return array
     * @throws \coding_exception
     */
    public static function get_version_menu_options(int $questionid): array {

        $versions = self::get_version_options($questionid);
        $latestversion = reset($versions);

        $return = [];

        // Add the "always latest" option to the beginning.
        $return[0] = get_string('alwayslatest', 'question');

        foreach ($versions as $version) {
            if ($version->version === $latestversion->version) {
                $value = get_string('versioninfolatestshort', 'question', $version->version);
            } else {
                $value = get_string('question_versionshort', 'question', $version->version);
            }
            $return[$version->version] = $value;
        }

        return $return;

    }

    /**
     * Get the available versions of a question where one of the version has the given question id.
     *
     * @param int $questionid id of a question.
     * @return stdClass[] other versions of this question. Each object has fields versionid,
     *       version and questionid. Array is returned most recent version first.
     */
    public static function get_version_options(int $questionid): array {
        global $DB;

        return $DB->get_records_sql("
                SELECT allversions.id AS versionid,
                       allversions.version,
                       allversions.questionid

                  FROM {question_versions} allversions

                 WHERE allversions.questionbankentryid = (
                            SELECT givenversion.questionbankentryid
                              FROM {question_versions} givenversion
                             WHERE givenversion.questionid = ?
                       )
                   AND allversions.status <> ?

              ORDER BY allversions.version DESC
              ", [$questionid, question_version_status::QUESTION_STATUS_DRAFT]);
    }

}
