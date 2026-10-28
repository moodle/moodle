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

namespace local_launchpad;

/**
 * Cohort label / slug validation against Moodle identifier limits.
 *
 * Rejects invalid input clearly; never silently substitutes a default slug.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class cohort {

    /** Moodle course.idnumber length (lib/db/install.xml). */
    public const COURSE_IDNUMBER_MAX = 100;

    /** Moodle course.shortname length. */
    public const COURSE_SHORTNAME_MAX = 255;

    /** Moodle course_modules.idnumber length. */
    public const CM_IDNUMBER_MAX = 255;

    /**
     * Longest course idnumber prefix in blueprint: "lp-fullstack-" / "lp-marketing-".
     * Slug must fit: prefix + slug <= COURSE_IDNUMBER_MAX.
     */
    public const MAX_SLUG_LENGTH = 87;

    /** Reasonable cohort display label (used in course fullname). */
    public const MAX_LABEL_LENGTH = 120;

    /**
     * Validate cohort label and all generated shortnames / idnumbers.
     *
     * @return string Normalized slug (never empty, never a silent fallback)
     * @throws cohort_exception
     */
    public static function validate(string $cohort): string {
        $label = trim($cohort);
        if ($label === '') {
            throw new cohort_exception(
                'Cohort label is empty. Pass a non-empty --cohort value (e.g. Pilot or 2026-Pilot).'
            );
        }
        // ASCII-oriented lengths (slug is [a-z0-9-]; label bound is conservative).
        if (strlen($label) > self::MAX_LABEL_LENGTH) {
            throw new cohort_exception(
                'Cohort label is too long (max ' . self::MAX_LABEL_LENGTH . ' characters after trim).'
            );
        }

        $slug = blueprint::cohort_slug($label);
        if (strlen($slug) > self::MAX_SLUG_LENGTH) {
            throw new cohort_exception(
                'Cohort slug "' . $slug . '" is too long ('
                . strlen($slug) . ' > ' . self::MAX_SLUG_LENGTH
                . '). Shorten --cohort so generated course idnumbers fit Moodle\'s '
                . self::COURSE_IDNUMBER_MAX . '-character limit.'
            );
        }

        self::assert_generated_identifiers($label);

        return $slug;
    }

    /**
     * @throws cohort_exception
     */
    public static function assert_generated_identifiers(string $cohort): void {
        foreach (blueprint::courses($cohort) as $course) {
            $short = $course['shortname'];
            $idn = $course['idnumber'];
            if (strlen($short) > self::COURSE_SHORTNAME_MAX) {
                throw new cohort_exception(
                    'Generated shortname exceeds ' . self::COURSE_SHORTNAME_MAX
                    . ' characters: ' . $short
                );
            }
            if (strlen($idn) > self::COURSE_IDNUMBER_MAX) {
                throw new cohort_exception(
                    'Generated course idnumber exceeds ' . self::COURSE_IDNUMBER_MAX
                    . ' characters: ' . $idn
                );
            }
            foreach (blueprint::activities_for($course['kind']) as $act) {
                $cm = $idn . '-' . $act['idnumber'];
                if (strlen($cm) > self::CM_IDNUMBER_MAX) {
                    throw new cohort_exception(
                        'Generated activity idnumber exceeds ' . self::CM_IDNUMBER_MAX
                        . ' characters: ' . $cm
                    );
                }
            }
        }
    }
}
