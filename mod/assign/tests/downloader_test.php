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

namespace mod_assign;

use context_module;
use assign;

/**
 * Downloader tests class for mod_assign.
 *
 * @package    mod_assign
 * @category   test
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_assign\downloader
 */
class downloader_test extends \advanced_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assign/locallib.php');
    }

    /**
     * Test for load_filelist method.
     *
     * @covers ::load_filelist
     * @dataProvider load_filelist_provider
     *
     * @param bool $teamsubmission if the assign must have team submissions
     * @param array $groupmembers the groups definition
     * @param array|null $filterusers the filtered users (null for all users)
     * @param bool $blindmarking if the assign has blind marking
     * @param bool $downloadasfolder if the download as folder preference is set
     * @param array $expected the expected file list
     */
    public function test_load_filelist(
        bool $teamsubmission,
        array $groupmembers,
        ?array $filterusers,
        bool $blindmarking,
        bool $downloadasfolder,
        array $expected
    ) {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        if (!$downloadasfolder) {
            set_user_preference('assign_downloadasfolders', 0);
        }

        // Create course and enrols.
        $course = $this->getDataGenerator()->create_course();
        $users = [
            'student1' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'student2' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'student3' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'student4' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
            'student5' => $this->getDataGenerator()->create_and_enrol($course, 'student'),
        ];

        // Generate groups.
        $groups = [];
        foreach ($groupmembers as $groupname => $groupusers) {
            $group = $this->getDataGenerator()->create_group(['courseid' => $course->id, 'name' => $groupname]);
            foreach ($groupusers as $user) {
                groups_add_member($group, $users[$user]);
            }
            $groups[$groupname] = $group;
        }

        // Create activity.
        $params = [
            'course' => $course,
            'assignsubmission_file_enabled' => 1,
            'assignsubmission_file_maxfiles' => 12,
            'assignsubmission_file_maxsizebytes' => 1024 * 1024,
        ];
        if ($teamsubmission) {
            $params['teamsubmission'] = 1;
            $params['preventsubmissionnotingroup'] = false;
        }
        if ($blindmarking) {
            $params['blindmarking'] = 1;
        }
        $activity = $this->getDataGenerator()->create_module('assign', $params);
        $cm = get_coursemodule_from_id('assign', $activity->cmid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        // Generate submissions.
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $files = [
            "mod/assign/tests/fixtures/submissionsample01.txt",
            "mod/assign/tests/fixtures/submissionsample02.txt"
        ];
        foreach ($users as $key => $user) {
            if ($key == 'student5') {
                continue;
            }
            $datagenerator->create_submission([
                'userid' => $user->id,
                'assignid' => $cm->id,
                'file' => implode(',', $files),
            ]);
        }

        // Generate file list.
        if ($filterusers) {
            foreach ($filterusers as $key => $identifier) {
                $filterusers[$key] = $users[$identifier]->id;
            }
        }
        $manager = new assign($context, $cm, $course);
        $downloader = new downloader($manager, $filterusers);
        $hasfiles = $downloader->load_filelist();

        // Expose protected filelist attribute.
        $rc = new \ReflectionClass(downloader::class);
        $rcp = $rc->getProperty('filesforzipping');

        // Add some replacements.
        $search = ['PARTICIPANT', 'DEFAULTTEAM'];
        $replace = [get_string('participant', 'mod_assign'), get_string('defaultteam', 'mod_assign')];
        foreach ($users as $identifier => $user) {
            $search[] = strtoupper($identifier . '.ID');
            $replace[] = $manager->get_uniqueid_for_user($user->id);
            $search[] = strtoupper($identifier);
            $replace[] = $this->prepare_filename_text(fullname($user));
        }
        foreach ($groups as $identifier => $group) {
            $search[] = strtoupper($identifier . '.ID');
            $replace[] = strtoupper($group->id);
            $search[] = strtoupper($identifier);
            $replace[] = $this->prepare_filename_text($group->name);
        }

        // Validate values.
        $filelist = $rcp->getValue($downloader);
        $result = array_keys($filelist);

        $this->assertEquals($hasfiles, !empty($expected));
        $this->assertCount(count($expected), $result);
        foreach ($expected as $path) {
            $value = str_replace($search, $replace, $path);
            $this->assertTrue(in_array($value, $result));
        }
    }

    /**
     * Internal helper to clean a filename text.
     *
     * @param string $text the text to transform
     * @return string the clean string
     */
    private function prepare_filename_text(string $text): string {
        return clean_filename(str_replace('_', ' ', $text));
    }

    /**
     * Data provider for test_load_filelist().
     *
     * @return array of scenarios
     */
    public function load_filelist_provider(): array {
        $downloadasfoldertests = $this->load_filelist_downloadasfolder_scenarios();
        $downloadasfilestests = $this->load_filelist_downloadasfiles_scenarios();
        return array_merge(
            $downloadasfoldertests,
            $downloadasfilestests,
        );
    }

    /**
     * Generate the standard test scenarios for load_filelist with download as file.
     *
     * The scenarios are the same as download as folder but replacing the "/" of the files
     * by a "_" and setting the downloadasfolder to false.
     *
     * @return array of scenarios
     */
    private function load_filelist_downloadasfiles_scenarios(): array {
        $result = $this->load_filelist_downloadasfolder_scenarios("Download as files:");
        // Transform paths from files.
        foreach ($result as $scenario => $info) {
            $info['downloadasfolder'] = false;
            foreach ($info['expected'] as $key => $path) {
                $info['expected'][$key] = str_replace('/', '_', $path);
            }
            $result[$scenario] = $info;
        }
        return $result;
    }

    /**
     * Generate the standard test scenarios for load_filelist with download as folder.
     *
     * @param string $prefix the scenarios prefix
     * @return array of scenarios
     */
    private function load_filelist_downloadasfolder_scenarios(string $prefix = "Download as folders:"): array {
        return [
            // Test without team submissions.
            $prefix . ' All users without groups' => [
                'teamsubmission' => false,
                'groupmembers' => [],
                'filterusers' => null,
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'STUDENT1_STUDENT1.ID_assignsubmission_file/submissionsample01.txt',
                    'STUDENT1_STUDENT1.ID_assignsubmission_file/submissionsample02.txt',
                    'STUDENT2_STUDENT2.ID_assignsubmission_file/submissionsample01.txt',
                    'STUDENT2_STUDENT2.ID_assignsubmission_file/submissionsample02.txt',
                    'STUDENT3_STUDENT3.ID_assignsubmission_file/submissionsample01.txt',
                    'STUDENT3_STUDENT3.ID_assignsubmission_file/submissionsample02.txt',
                    'STUDENT4_STUDENT4.ID_assignsubmission_file/submissionsample01.txt',
                    'STUDENT4_STUDENT4.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtered users' => [
                'teamsubmission' => false,
                'groupmembers' => [],
                'filterusers' => ['student1', 'student2'],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'STUDENT1_STUDENT1.ID_assignsubmission_file/submissionsample01.txt',
                    'STUDENT1_STUDENT1.ID_assignsubmission_file/submissionsample02.txt',
                    'STUDENT2_STUDENT2.ID_assignsubmission_file/submissionsample01.txt',
                    'STUDENT2_STUDENT2.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtering users without submissions' => [
                'teamsubmission' => false,
                'groupmembers' => [],
                'filterusers' => ['student1', 'student5'],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'STUDENT1_STUDENT1.ID_assignsubmission_file/submissionsample01.txt',
                    'STUDENT1_STUDENT1.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Asking only for users without submissions' => [
                'teamsubmission' => false,
                'groupmembers' => [],
                'filterusers' => ['student5'],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [],
            ],
            // Test with team submissions and no default team.
            $prefix . ' All users with all users in groups' => [
                'teamsubmission' => true,
                'groupmembers' => [
                    'group1' => ['student1'],
                    'group2' => ['student2', 'student3'],
                    'group3' => ['student4', 'student5'],
                ],
                'filterusers' => null,
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample02.txt',
                    'GROUP2_GROUP2.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP2_GROUP2.ID_assignsubmission_file/submissionsample02.txt',
                    'GROUP3_GROUP3.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP3_GROUP3.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtering users with disjoined groups' => [
                'teamsubmission' => true,
                'groupmembers' => [
                    'group1' => ['student1'],
                    'group2' => ['student2', 'student3'],
                    'group3' => ['student4', 'student5'],
                ],
                'filterusers' => ['student1', 'student2'],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample02.txt',
                    'GROUP2_GROUP2.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP2_GROUP2.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtering users with default teams who does not do a submission' => [
                'teamsubmission' => true,
                'groupmembers' => [
                    'group1' => ['student1'],
                    'group2' => ['student2', 'student3'],
                    'group3' => ['student4', 'student5'],
                ],
                'filterusers' => ['student1', 'student5'],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample02.txt',
                    'GROUP3_GROUP3.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP3_GROUP3.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtering users without submission but member of a group' => [
                'teamsubmission' => true,
                'groupmembers' => [
                    'group1' => ['student1'],
                    'group2' => ['student2', 'student3'],
                    'group3' => ['student4', 'student5'],
                ],
                'filterusers' => [
                    'student5'
                ],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'GROUP3_GROUP3.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP3_GROUP3.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            // Test with default team.
            $prefix . ' All users with users in the default team' => [
                'teamsubmission' => true,
                'groupmembers' => [
                    'group1' => ['student1', 'student2'],
                ],
                'filterusers' => null,
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample02.txt',
                    'DEFAULTTEAM_assignsubmission_file/submissionsample01.txt',
                    'DEFAULTTEAM_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtered users in groups with users in the default team' => [
                'teamsubmission' => true,
                'groupmembers' => [
                    'group1' => ['student1', 'student2'],
                ],
                'filterusers' => ['student1', 'student2'],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtered users without groups with users in the default team' => [
                'teamsubmission' => true,
                'groupmembers' => [
                    'group1' => ['student1', 'student2'],
                ],
                'filterusers' => ['student3', 'student4'],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'DEFAULTTEAM_assignsubmission_file/submissionsample01.txt',
                    'DEFAULTTEAM_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtered users with some users in the default team' => [
                'teamsubmission' => true,
                'groupmembers' => [
                    'group1' => ['student1', 'student2'],
                ],
                'filterusers' => ['student1', 'student3'],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample02.txt',
                    'DEFAULTTEAM_assignsubmission_file/submissionsample01.txt',
                    'DEFAULTTEAM_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtering users with joined groups' => [
                'teamsubmission' => true,
                'groupmembers' => [
                    'group1' => ['student1', 'student2'],
                    'group2' => ['student2', 'student3'],
                ],
                'filterusers' => ['student1', 'student2'],
                'blindmarking' => false,
                'downloadasfolder' => true,
                'expected' => [
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample01.txt',
                    'GROUP1_GROUP1.ID_assignsubmission_file/submissionsample02.txt',
                    'DEFAULTTEAM_assignsubmission_file/submissionsample01.txt',
                    'DEFAULTTEAM_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            // Tests with blind marking.
            $prefix . ' All users without groups and blindmarking' => [
                'teamsubmission' => false,
                'groupmembers' => [],
                'filterusers' => null,
                'blindmarking' => true,
                'downloadasfolder' => true,
                'expected' => [
                    'PARTICIPANT_STUDENT1.ID_assignsubmission_file/submissionsample01.txt',
                    'PARTICIPANT_STUDENT1.ID_assignsubmission_file/submissionsample02.txt',
                    'PARTICIPANT_STUDENT2.ID_assignsubmission_file/submissionsample01.txt',
                    'PARTICIPANT_STUDENT2.ID_assignsubmission_file/submissionsample02.txt',
                    'PARTICIPANT_STUDENT3.ID_assignsubmission_file/submissionsample01.txt',
                    'PARTICIPANT_STUDENT3.ID_assignsubmission_file/submissionsample02.txt',
                    'PARTICIPANT_STUDENT4.ID_assignsubmission_file/submissionsample01.txt',
                    'PARTICIPANT_STUDENT4.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
            $prefix . ' Filtered users without groups and blindmarking' => [
                'teamsubmission' => false,
                'groupmembers' => [],
                'filterusers' => ['student1', 'student2'],
                'blindmarking' => true,
                'downloadasfolder' => true,
                'expected' => [
                    'PARTICIPANT_STUDENT1.ID_assignsubmission_file/submissionsample01.txt',
                    'PARTICIPANT_STUDENT1.ID_assignsubmission_file/submissionsample02.txt',
                    'PARTICIPANT_STUDENT2.ID_assignsubmission_file/submissionsample01.txt',
                    'PARTICIPANT_STUDENT2.ID_assignsubmission_file/submissionsample02.txt',
                ],
            ],
        ];
    }
}
