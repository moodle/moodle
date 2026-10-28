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
 * Declarative LaunchPad category/course/section/activity blueprint.
 *
 * Titles only — no curriculum body. Aligned with ToBa_Learn_MVP.pdf
 * (MVP 1 structure: hidden fellowship category, five track categories,
 * five track courses, optional Orientation course under the fellowship root).
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class blueprint {
    /** Top-level fellowship category name. */
    public const ROOT_CATEGORY = 'ToBa Tech LaunchPad Fellowship';

    /** Root category idnumber. */
    public const ROOT_IDNUMBER = 'launchpad-root';

    /**
     * Five track sub-categories (idnumber => name).
     * Orientation is a course under the root, not a sixth track category (MVP §4).
     *
     * @return array<string, string>
     */
    public static function track_categories(): array {
        return [
            'launchpad-ai' => 'AI & Automation',
            'launchpad-fullstack' => 'Full Stack Development',
            'launchpad-pm' => 'Product Management',
            'launchpad-marketing' => 'Digital Marketing',
            'launchpad-startup' => 'Startup Building',
        ];
    }

    /**
     * Four phase section names (section index 1..4).
     *
     * @return array<int, string>
     */
    public static function phase_sections(): array {
        return [
            1 => 'Foundations',
            2 => 'Guided project',
            3 => 'Industry simulation',
            4 => 'Demo day and certification',
        ];
    }

    /**
     * Sanitize cohort label for shortnames / idnumbers.
     *
     * Does not substitute a default slug when input is invalid — callers must
     * validate via {@see cohort::validate()} (CLI) or handle {@see cohort_exception}.
     *
     * @throws cohort_exception when the label yields an empty slug
     */
    public static function cohort_slug(string $cohort): string {
        $slug = strtolower(trim($cohort));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');
        if ($slug === '') {
            throw new cohort_exception(
                'Cohort label "' . trim($cohort) . '" produces an empty slug. '
                . 'Use letters or digits (e.g. Pilot, 2026-Pilot).'
            );
        }
        return $slug;
    }

    /**
     * Course definitions for one cohort.
     *
     * @return list<array{
     *   category_idnumber: string,
     *   shortname: string,
     *   idnumber: string,
     *   fullname: string,
     *   summary: string,
     *   kind: string
     * }>
     */
    public static function courses(string $cohort): array {
        $label = trim($cohort);
        $slug = self::cohort_slug($label);

        // Shared orientation lives under the fellowship root (MVP §4), not a track category.
        $courses = [
            [
                'category_idnumber' => self::ROOT_IDNUMBER,
                'shortname' => 'LP-ORIENT-' . strtoupper($slug),
                'idnumber' => 'lp-orient-' . $slug,
                'fullname' => 'LaunchPad Orientation — Cohort ' . $label,
                'summary' => 'Shared LaunchPad orientation. Placeholder structure only — curriculum pending approval.',
                'kind' => 'orientation',
            ],
        ];

        foreach (self::track_categories() as $catid => $trackname) {
            $codeslug = str_replace('launchpad-', '', $catid);
            $summary = 'LaunchPad track course (hidden until release). Placeholder structure only.';
            if ($catid === 'launchpad-fullstack') {
                $summary .= ' A laptop is required for later build work (stated for fellows before placement).';
            }
            $courses[] = [
                'category_idnumber' => $catid,
                'shortname' => 'LP-' . strtoupper($codeslug) . '-' . strtoupper($slug),
                'idnumber' => 'lp-' . $codeslug . '-' . $slug,
                'fullname' => 'LaunchPad — ' . $trackname . ' — Cohort ' . $label,
                'summary' => $summary,
                'kind' => 'track',
            ];
        }

        return $courses;
    }

    /**
     * Placeholder activities for a course kind.
     *
     * Each item: section (0=general, 1-4=phases), modulename, name, idnumber suffix.
     *
     * @return list<array{section:int, modulename:string, name:string, idnumber:string}>
     */
    public static function activities_for(string $kind): array {
        $placeholder = ' [placeholder — content pending]';

        if ($kind === 'orientation') {
            return [
                [
                    'section' => 0,
                    'modulename' => 'page',
                    'name' => 'Orientation overview' . $placeholder,
                    'idnumber' => 'act-orient-overview',
                ],
                [
                    'section' => 0,
                    'modulename' => 'quiz',
                    'name' => 'Orientation check' . $placeholder,
                    'idnumber' => 'act-orient-quiz',
                ],
            ];
        }

        // Track course: general + four phases with approved activity types only.
        return [
            [
                'section' => 0,
                'modulename' => 'page',
                'name' => 'How this course works' . $placeholder,
                'idnumber' => 'act-how',
            ],
            [
                'section' => 1,
                'modulename' => 'page',
                'name' => 'Foundations lesson' . $placeholder,
                'idnumber' => 'act-fnd-page',
            ],
            [
                'section' => 1,
                'modulename' => 'quiz',
                'name' => 'Foundations check' . $placeholder,
                'idnumber' => 'act-fnd-quiz',
            ],
            [
                'section' => 2,
                'modulename' => 'page',
                'name' => 'Guided project brief' . $placeholder,
                'idnumber' => 'act-gp-page',
            ],
            [
                'section' => 2,
                'modulename' => 'assign',
                'name' => 'Guided project submission' . $placeholder,
                'idnumber' => 'act-gp-assign',
            ],
            [
                'section' => 3,
                'modulename' => 'page',
                'name' => 'Industry simulation brief' . $placeholder,
                'idnumber' => 'act-sim-page',
            ],
            [
                'section' => 3,
                'modulename' => 'assign',
                'name' => 'Industry simulation submission' . $placeholder,
                'idnumber' => 'act-sim-assign',
            ],
            [
                'section' => 4,
                'modulename' => 'page',
                'name' => 'Demo day instructions' . $placeholder,
                'idnumber' => 'act-demo-page',
            ],
            [
                'section' => 4,
                'modulename' => 'assign',
                'name' => 'Capstone and demo submission' . $placeholder,
                'idnumber' => 'act-demo-assign',
            ],
        ];
    }
}
