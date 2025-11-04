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

namespace core_backup;

use advanced_testcase;
use backup;
use backup_plan_dbops;
use core\exception\coding_exception;
use core_courseformat\local\sectionactions;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

/**
 * Tests related to the default backup filename feature.
 *
 * @package    core_backup
 * @copyright  2025 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class default_backup_filename_test extends advanced_testcase {
    /**
     * Provides backup filename scenarios.
     * @return array
     */
    public static function get_default_backup_filename_provider(): array {
        return [
            // This first block of tests are tests that confirm the new mustache
            // rendering method internally produces the same output as the original function.
            'moodle format, course, with users, anonymised, use id only, with files' => [
                'generate' => [
                    'course' => [
                        'shortname' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-course-{{courseid}}-19700101-0800-an.mbz',
            ],
            'moodle format, course, without users, anonymised, use id only, with files' => [
                'generate' => [
                    'course' => [
                        'shortname' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => false,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-course-{{courseid}}-19700101-0800-nu.mbz',
            ],
            'moodle format, course, ith users, not anonymised, use id only, with files' => [
                'generate' => [
                    'course' => [
                        'shortname' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => false,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-course-{{courseid}}-19700101-0800.mbz',
            ],
            'moodle format, course, with users, anonymised, use id only, without files' => [
                'generate' => [
                    'course' => [
                        'shortname' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => false,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-course-{{courseid}}-19700101-0800-an-nf.mbz',
            ],
            'moodle format, course, with users, anonymised, not id only, with files' => [
                'generate' => [
                    'course' => [
                        'shortname' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => false,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-course-{{courseid}}-abc123-19700101-0800-an.mbz',
            ],
            'moodle format, section, with users, anonymised, use id only, with files' => [
                'generate' => [
                    'section' => [
                        'name' => 'abc123',
                        'section' => 9,
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1SECTION,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-section-{{sectionid}}-19700101-0800-an.mbz',
            ],
            'moodle format, section, without users, anonymised, use id only, with files' => [
                'generate' => [
                    'section' => [
                        'name' => 'abc123',
                        'section' => 9,
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1SECTION,
                    'users' => false,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-section-{{sectionid}}-19700101-0800-nu.mbz',
            ],
            'moodle format, section, with users, not anonymised, use id only, with files' => [
                'generate' => [
                    'section' => [
                        'name' => 'abc123',
                        'section' => 9,
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1SECTION,
                    'users' => true,
                    'anonymised' => false,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-section-{{sectionid}}-19700101-0800.mbz',
            ],
            'moodle format, section, with users, anonymised, without use id only (with section name), with files' => [
                'generate' => [
                    'section' => [
                        'name' => 'abc123',
                        'section' => 9,
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1SECTION,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => false,
                    'files' => true,
                ],
                'customtemplates' => [],
                // Where section has name, it uses the name.
                'expectedfilename' => 'backup-moodle2-section-{{sectionid}}-abc123-19700101-0800-an.mbz',
            ],
            'moodle format, section, with users, anonymised, without use id only (no section name), with files' => [
                'generate' => [
                    'section' => [
                        'section' => 9,
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1SECTION,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => false,
                    'files' => true,
                ],
                'customtemplates' => [],
                // Section has no name, it uses the number instead.
                'expectedfilename' => 'backup-moodle2-section-{{sectionid}}-9-19700101-0800-an.mbz',
            ],
            'moodle format, section, with users, anonymised, with use id only, without files' => [
                'generate' => [
                    'section' => [
                        'name' => 'abc123',
                        'section' => 9,
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1SECTION,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => false,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-section-{{sectionid}}-19700101-0800-an-nf.mbz',
            ],
            'moodle format, activity, with users, anonymised, use id only, with files' => [
                'generate' => [
                    'activity' => [
                        'name' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1ACTIVITY,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-activity-{{activitycmid}}-19700101-0800-an.mbz',
            ],
            'moodle format, activity, without users, anonymised, use id only, with files' => [
                'generate' => [
                    'activity' => [
                        'name' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1ACTIVITY,
                    'users' => false,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-activity-{{activitycmid}}-19700101-0800-nu.mbz',
            ],
            'moodle format, activity, with users, not anonymised, use id only, with files' => [
                'generate' => [
                    'activity' => [
                        'name' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1ACTIVITY,
                    'users' => true,
                    'anonymised' => false,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-activity-{{activitycmid}}-19700101-0800.mbz',
            ],
            'moodle format, activity, with users, anonymised, without use id only, with files' => [
                'generate' => [
                    'activity' => [
                        'name' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1ACTIVITY,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => false,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-activity-{{activitycmid}}-page{{activitycmid}}-19700101-0800-an.mbz',
            ],
            'moodle format, activity, with users, anonymised, with id only, without files' => [
                'generate' => [
                    'activity' => [
                        'name' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1ACTIVITY,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => false,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-activity-{{activitycmid}}-19700101-0800-an-nf.mbz',
            ],

            // This second block tests custom template functions being used.
            'custom template - course' => [
                'generate' => [
                    'course' => [
                        'shortname' => 'shortcourse',
                        'fullname' => 'fullcourse',
                        'startdate' => 5000,
                        'enddate' => 10000,
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [
                    'backup_default_filename_template_course' =>
                        '{{course.shortname}}-{{course.fullname}}-{{id}}-{{date}}-{{course.startdate}}-{{course.enddate}}',
                ],
                'expectedfilename' => 'shortcourse-fullcourse-{{courseid}}-19700101-0800-19700101-0923-19700101-1046.mbz',
            ],
            'custom template - section' => [
                'generate' => [
                    'section' => [
                        'name' => 'section123',
                        'section' => '1',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1SECTION,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [
                    'backup_default_filename_template_section' => '{{section.name}}-{{section.section}}-{{id}}-{{date}}',
                ],
                'expectedfilename' => 'section123-1-{{sectionid}}-19700101-0800.mbz',
            ],
            'custom template - activity' => [
                'generate' => [
                    'activity' => [
                        'name' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1ACTIVITY,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [
                    'backup_default_filename_template_activity' => '{{activity.modname}}-{{activity.name}}-{{id}}-{{date}}',
                ],
                'expectedfilename' => 'page-abc123-{{activitycmid}}-19700101-0800.mbz',
            ],

            // This third block tests various edge cases.
            'spaces in context values and custom template are replaced' => [
                'generate' => [
                    'course' => [
                        'shortname' => 'a b c',
                        'fullname' => 'x y z',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [
                    'backup_default_filename_template_course' => '{{course.shortname}}   {{course.fullname}}',
                ],
                'expectedfilename' => 'a_b_c___x_y_z.mbz',
            ],
            'spaces in context values without custom template are replaced' => [
                'generate' => [
                    'course' => [
                        'shortname' => 'a b c',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => false,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-course-{{courseid}}-a_b_c-19700101-0800-an.mbz',
            ],
            'whitespace trimmed from context values with custom template' => [
                'generate' => [
                    'course' => [
                        'shortname' => " abc \n",
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [
                    'backup_default_filename_template_course' => '{{course.shortname}}',
                ],
                'expectedfilename' => 'abc.mbz',
            ],
            'whitespace trimmed from context values without custom template' => [
                'generate' => [
                    'course' => [
                        'shortname' => " abc \n",
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => false,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-course-{{courseid}}-abc-19700101-0800-an.mbz',
            ],
            'format string applied to context values with custom template' => [
                'generate' => [
                    'course' => [
                        // Format_string will remove the link and newline before using.
                        'shortname' => "<a href='test'/>\nabc",
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [
                    'backup_default_filename_template_course' => '{{course.shortname}}',
                ],
                'expectedfilename' => 'abc.mbz',
            ],
            'format string applied to context values without custom template' => [
                'generate' => [
                    'course' => [
                        // Format_string will remove the link and newline before using.
                        'shortname' => "<a href='test'/>\nabc",
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => false,
                    'files' => true,
                ],
                'customtemplates' => [],
                'expectedfilename' => 'backup-moodle2-course-{{courseid}}-abc-19700101-0800-an.mbz',
            ],
            'generated name > 251 chars is truncated' => [
                'generate' => [
                    'course' => [
                        'shortname' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod " .
                            "tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis " .
                            "nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor.",
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [
                    'backup_default_filename_template_course' => '{{course.shortname}}',
                ],
                'expectedfilename' => 'Lorem_ipsum_dolor_sit_amet,_consectetur_adipiscing_elit,_sed_do_eiusmod_' .
                    'tempor_incididunt_ut_labore_et_dolore_magna_aliqua._Ut_enim_ad_minim_veniam,_quis_nostrud_' .
                    'exercitation_ullamco_laboris_nisi_ut_aliquip_ex_ea_commodo_consequat._Duis_aute_irure_dol.mbz',
            ],
            'course custom template is invalid, falls back to default' => [
                'generate' => [
                    'course' => [
                        'shortname' => 'abc123',
                    ],
                ],
                'params' => [
                    'format' => backup::FORMAT_MOODLE,
                    'type' => backup::TYPE_1COURSE,
                    'users' => true,
                    'anonymised' => true,
                    'useidonly' => true,
                    'files' => true,
                ],
                'customtemplates' => [
                    'backup_default_filename_template_course' => '{{',
                ],
                'expectedfilename' => 'backup-moodle2-course-{{courseid}}-19700101-0800-an.mbz',
            ],
        ];
    }

    /**
     * Tests get_default_backup_filename.
     *
     * @param array $generate array of resources to generate (courses, sections, activities).
     * @param array $params parameters to pass into get_default_backup_filename.
     * @param array $customtemplates array of key value pairs of config values, for setting the custom template config.
     * @param string $expectedfilename the filename expected to be generated.
     * @dataProvider get_default_backup_filename_provider
     * @covers \backup_plan_dbops::get_default_backup_filename
     */
    public function test_get_default_backup_filename(
        array $generate,
        array $params,
        array $customtemplates,
        string $expectedfilename
    ): void {
        $this->resetAfterTest(true);

        foreach ($customtemplates as $config => $value) {
            set_config($config, $value, 'backup');
        }

        // All types need a course.
        $course = $this->getDataGenerator()->create_course($generate['course'] ?? null);

        switch ($params['type']) {
            case backup::TYPE_1COURSE:
                $params['id'] = $course->id;
                $expectedfilename = str_replace('{{courseid}}', $course->id, $expectedfilename);
                break;
            case backup::TYPE_1SECTION:
                $sectioninfo = $this->getDataGenerator()->create_course_section(['course' => $course, 'section' =>
                    $generate['section']['section']]);
                $actions = new sectionactions($course);
                $actions->update($sectioninfo, $generate['section'] ?? []);
                $params['id'] = $sectioninfo->id;
                $sectioninfo = get_fast_modinfo($course)->get_section_info($sectioninfo->sectionnum);
                $expectedfilename = str_replace('{{sectionid}}', $sectioninfo->id, $expectedfilename);
                break;
            case backup::TYPE_1ACTIVITY:
                $activity = $this->getDataGenerator()->create_module(
                    'page',
                    array_merge(['course' => $course->id], $generate['activity'])
                );
                $params['id'] = $activity->cmid;
                $expectedfilename = str_replace('{{activitycmid}}', $activity->cmid, $expectedfilename);
                break;
            default:
                throw new coding_exception("Unhandled backup type " . $params['type']);
        }

        $defaultfilename = backup_plan_dbops::get_default_backup_filename(
            $params['format'],
            $params['type'],
            $params['id'],
            $params['users'],
            $params['anonymised'],
            $params['useidonly'],
            $params['files'],
            0
        );
        $this->assertEquals($expectedfilename, $defaultfilename);
    }

    /**
     * Tests getting syntax errors in template.
     *
     * @covers \backup_plan_dbops::get_default_backup_filename_template_syntax_errors
     */
    public function test_get_default_backup_filename_syntax_errors(): void {
        $this->assertEmpty(backup_plan_dbops::get_default_backup_filename_template_syntax_errors("this is ok {{test}}"));
        $this->assertNotEmpty(backup_plan_dbops::get_default_backup_filename_template_syntax_errors("this is invalid {{"));
    }
}
