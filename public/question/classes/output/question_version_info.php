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

namespace core_question\output;

use renderer_base;

/**
 * Track and display question version information.
 *
 * This class handles rendering the question version information (the current version of the question, the total number of versions,
 * and if the current version is the latest). It also tracks loaded question definitions that don't yet have the latest version
 * loaded, and handles loading the latest version of all pending questions.
 *
 * @package   core_question
 * @copyright 2023 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class question_version_info implements \renderable, \templatable {

    /**
     * @var array List of definitions that don't know whether they are the latest version yet.
     */
    public static array $pendingdefinitions = [];

    /**
     * @var int $version The current version number.
     */
    public int $version;

    /**
     * @var ?int $latestversion The latest version number of this question.
     */
    public ?int $latestversion;

    /**
     * @var bool $shortversion Are we displaying an abbreviation for "version" rather than the full word?
     */
    protected bool $shortversion;

    /**
     * Store the current and latest versions of the question, and whether we want to abbreviate the output string.
     *
     * @param \question_definition $question
     * @param bool $shortversion
     */
    public function __construct(\question_definition $question, bool $shortversion = false) {
        $this->version = $question->version;
        $this->latestversion = $question->latestversion;
        $this->shortversion = $shortversion;
    }

    /**
     * Find and set the latest version of all pending question_definition objects.
     *
     * This will update all pending objects in one go, saving us having to do a query for each question.
     *
     * @return void
     */
    public static function populate_latest_versions(): void {
        global $DB;
        $pendingentryids = array_map(fn($definition) => $definition->questionbankentryid, self::$pendingdefinitions);
        [$insql, $params] = $DB->get_in_or_equal($pendingentryids);

        $sql = "SELECT questionbankentryid, MAX(version) AS latestversion
                      FROM {question_versions}
                     WHERE questionbankentryid $insql
                  GROUP BY questionbankentryid";
        $latestversions = $DB->get_records_sql_menu($sql, $params);
        array_walk(self::$pendingdefinitions, function($definition) use ($latestversions) {
            if (!isset($latestversions[$definition->questionbankentryid])) {
                return;
            }
            $definition->set_latest_version($latestversions[$definition->questionbankentryid]);
            unset(self::$pendingdefinitions[$definition->id]);
        });
    }

    /**
     * Return the question version info as a string, including the version number and whether this is the latest version.
     *
     * @param renderer_base $output
     * @return array
     * @throws \coding_exception
     */
    public function export_for_template(renderer_base $output): array {
        if (is_null($this->latestversion)) {
            return [];
        }
        $identifier = 'versioninfo';
        if ($this->version === $this->latestversion) {
            $identifier .= 'latest';
        }
        if ($this->shortversion) {
            $identifier = 'short' . $identifier;
        }
        return [
            'versioninfo' => get_string($identifier, 'question', $this)
        ];
    }
}
