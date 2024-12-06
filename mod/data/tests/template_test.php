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

namespace mod_data;

use context_module;
use rating_manager;
use stdClass;

/**
 * Template tests class for mod_data.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \mod_data\template
 */
final class template_test extends \advanced_testcase {
    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/rating/lib.php');
    }

    /**
     * Test for static create methods.
     *
     * @covers ::parse_entries
     * @dataProvider parse_entries_provider
     * @param string $templatecontent the template string
     * @param string $expected expected output
     * @param string $rolename the user rolename
     * @param bool $enableexport is portfolio export is enabled
     * @param bool $approved if the entry is approved
     * @param bool $enablecomments is comments are enabled
     * @param bool $enableratings if ratings are enabled
     * @param array $options extra parser options
     * @param bool $otherauthor if the entry is from another user
     */
    public function test_parse_entries(
        string $templatecontent,
        string $expected,
        string $rolename = 'editingteacher',
        bool $enableexport = false,
        bool $approved = true,
        bool $enablecomments = false,
        bool $enableratings = false,
        array $options = [],
        bool $otherauthor = false
    ): void {
        global $DB, $PAGE;
        // Comments, tags, approval, user role.
        $this->resetAfterTest();

        $params = ['approval' => true];

        // Enable comments.
        if ($enablecomments) {
            set_config('usecomments', 1);
            $params['comments'] = true;
            $PAGE->reset_theme_and_output();
            $PAGE->set_url('/mod/data/view.php');
        }

        $course = $this->getDataGenerator()->create_course();
        $params['course'] = $course;
        $activity = $this->getDataGenerator()->create_module('data', $params);
        $cm = get_coursemodule_from_id('data', $activity->cmid, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);

        $user = $this->getDataGenerator()->create_user();
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $roleids[$rolename]);
        $author = $user;

        if ($otherauthor) {
            $user2 = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user2->id, $course->id, $roleids[$rolename]);
            $author = $user2;
        }

        // Generate an entry.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $fieldrecord = (object)['name' => 'myfield', 'type' => 'text'];
        $field = $generator->create_field($fieldrecord, $activity);
        $otherfieldrecord = (object)['name' => 'otherfield', 'type' => 'text'];
        $otherfield = $generator->create_field($otherfieldrecord, $activity);

        $this->setUser($user);

        $entryid = $generator->create_entry(
            $activity,
            [
                $field->field->id => 'Example entry',
                $otherfield->field->id => 'Another example',
            ],
            0,
            ['Cats', 'Dogs'],
            ['approved' => $approved]
        );

        if ($enableexport) {
            $this->enable_portfolio($user);
        }

        $manager = manager::create_from_instance($activity);

        $entry = (object)[
            'id' => $entryid,
            'approved' => $approved,
            'timecreated' => 1657618639,
            'timemodified' => 1657618650,
            'userid' => $author->id,
            'groupid' => 0,
            'dataid' => $activity->id,
            'picture' => 0,
            'firstname' => $author->firstname,
            'lastname' => $author->lastname,
            'firstnamephonetic' => $author->firstnamephonetic,
            'lastnamephonetic' => $author->lastnamephonetic,
            'middlename' => $author->middlename,
            'alternatename' => $author->alternatename,
            'imagealt' => 'PIXEXAMPLE',
            'email' => $author->email,
        ];
        $entries = [$entry];

        if ($enableratings) {
            $entries = $this->enable_ratings($context, $activity, $entries, $user);
        }

        // Some cooked variables for the regular expression.
        $replace = [
            '{authorfullname}' => fullname($author),
            '{timeadded}' => userdate($entry->timecreated, get_string('strftimedatemonthabbr', 'langconfig')),
            '{timemodified}' => userdate($entry->timemodified, get_string('strftimedatemonthabbr', 'langconfig')),
            '{fieldid}' => $field->field->id,
            '{fieldname}' => $field->field->name,
            '{fielddescription}' => $field->field->description,
            '{entryid}' => $entry->id,
            '{cmid}' => $cm->id,
            '{courseid}' => $course->id,
            '{authorid}' => $author->id
        ];

        $parser = new template($manager, $templatecontent, $options);
        $result = $parser->parse_entries($entries);

        // We don't want line breaks for the validations.
        $result = str_replace("\n", '', $result);
        $regexp = str_replace(array_keys($replace), array_values($replace), $expected);
        $this->assertMatchesRegularExpression($regexp, $result);
    }

    /**
     * Data provider for test_parse_entries().
     *
     * @return array of scenarios
     */
    public static function parse_entries_provider(): array {
        return [
            // Teacher scenarios.
            'Teacher id tag' => [
                'templatecontent' => 'Some ##id## tag',
                'expected' => '|Some {entryid} tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher delete tag' => [
                'templatecontent' => 'Some ##delete## tag',
                'expected' => '|Some .*delete.*{entryid}.*sesskey.*Delete.* tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher edit tag' => [
                'templatecontent' => 'Some ##edit## tag',
                'expected' => '|Some .*edit.*{entryid}.*sesskey.*Edit.* tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher more tag' => [
                'templatecontent' => 'Some ##more## tag',
                'expected' => '|Some .*more.*{cmid}.*rid.*{entryid}.*More.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => true],
            ],
            'Teacher more tag with showmore set to false' => [
                'templatecontent' => 'Some ##more## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => false],
            ],
            'Teacher moreurl tag' => [
                'templatecontent' => 'Some ##moreurl## tag',
                'expected' => '|Some .*/mod/data/view.*{cmid}.*rid.*{entryid}.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => true],
            ],
            'Teacher moreurl tag with showmore set to false' => [
                'templatecontent' => 'Some ##moreurl## tag',
                'expected' => '|Some .*/mod/data/view.*{cmid}.*rid.*{entryid}.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => false],
            ],
            'Teacher delcheck tag' => [
                'templatecontent' => 'Some ##delcheck## tag',
                'expected' => '|Some .*input.*checkbox.*value.*{entryid}.* tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher user tag' => [
                'templatecontent' => 'Some ##user## tag',
                'expected' => '|Some .*user/view.*{authorid}.*course.*{courseid}.*{authorfullname}.* tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher userpicture tag' => [
                'templatecontent' => 'Some ##userpicture## tag',
                'expected' => '|Some .*user/view.*{authorid}.*course.*{courseid}.* tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher export tag' => [
                'templatecontent' => 'Some ##export## tag',
                'expected' => '|Some .*portfolio/add.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => true,
            ],
            'Teacher export tag not configured' => [
                'templatecontent' => 'Some ##export## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
            ],
            'Teacher timeadded tag' => [
                'templatecontent' => 'Some ##timeadded## tag',
                'expected' => '|Some <span.*>{timeadded}</span> tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher timemodified tag' => [
                'templatecontent' => 'Some ##timemodified## tag',
                'expected' => '|Some <span.*>{timemodified}</span> tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher approve tag approved entry' => [
                'templatecontent' => 'Some ##approve## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
            ],
            'Teacher approve tag disapproved entry' => [
                'templatecontent' => 'Some ##approve## tag',
                'expected' => '|Some .*approve.*{entryid}.*sesskey.*Approve.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => false,
            ],
            'Teacher disapprove tag approved entry' => [
                'templatecontent' => 'Some ##disapprove## tag',
                'expected' => '|Some .*disapprove.*{entryid}.*sesskey.*Undo approval.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
            ],
            'Teacher disapprove tag disapproved entry' => [
                'templatecontent' => 'Some ##disapprove## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => false,
            ],
            'Teacher approvalstatus tag approved entry' => [
                'templatecontent' => 'Some ##approvalstatus## tag',
                'expected' => '|Some  tag|', // We do not display the approval status anymore.
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
            ],
            'Teacher approvalstatus tag disapproved entry' => [
                'templatecontent' => 'Some ##approvalstatus## tag',
                'expected' => '|Some .*Pending approval.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => false,
            ],
            'Teacher approvalstatusclass tag approved entry' => [
                'templatecontent' => 'Some ##approvalstatusclass## tag',
                'expected' => '|Some approved tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
            ],
            'Teacher approvalstatusclass tag disapproved entry' => [
                'templatecontent' => 'Some ##approvalstatusclass## tag',
                'expected' => '|Some notapproved tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => false,
            ],
            'Teacher tags tag' => [
                'templatecontent' => 'Some ##tags## tag',
                'expected' => '|Some .*Cats.* tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher field name tag' => [
                'templatecontent' => 'Some [[myfield]] tag',
                'expected' => '|Some .*Example entry.* tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher field#id tag' => [
                'templatecontent' => 'Some [[myfield#id]] tag',
                'expected' => '|Some {fieldid} tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher field#name tag' => [
                'templatecontent' => 'Some [[myfield#name]] tag',
                'expected' => '|Some {fieldname} tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher field#description tag' => [
                'templatecontent' => 'Some [[myfield#description]] tag',
                'expected' => '|Some {fielddescription} tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher comments name tag with comments enabled' => [
                'templatecontent' => 'Some ##comments## tag',
                'expected' => '|Some .*Comments.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => true,
            ],
            'Teacher comments name tag with comments disabled' => [
                'templatecontent' => 'Some ##comments## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
            ],
            'Teacher comment forced with comments enables' => [
                'templatecontent' => 'No tags',
                'expected' => '|No tags.*Comments.*|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => true,
                'enableratings' => false,
                'options' => ['comments' => true],
            ],
            'Teacher comment forced without comments enables' => [
                'templatecontent' => 'No tags',
                'expected' => '|^No tags$|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['comments' => true],
            ],
            'Teacher adding ratings without ratings configured' => [
                'templatecontent' => 'No tags',
                'expected' => '|^No tags$|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['ratings' => true],
            ],
            'Teacher adding ratings with ratings configured' => [
                'templatecontent' => 'No tags',
                'expected' => '|^No tags.*Average of ratings|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => true,
                'options' => ['ratings' => true],
            ],
            'Teacher actionsmenu tag with default options' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|Some .*edit.*{entryid}.*Edit.* .*delete.*{entryid}.*Delete.* tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher actionsmenu tag with default options (check Show more is not there)' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|^Some((?!Show more).)*tag$|',
                'rolename' => 'editingteacher',
            ],
            'Teacher actionsmenu tag with show more enabled' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|Some .*view.*{cmid}.*rid.*{entryid}.*Show more.* .*Edit.* .*Delete.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => false,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => true],
            ],
            'Teacher actionsmenu tag with export enabled' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|Some .*Edit.* .*Delete.* .*portfolio/add.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => true,
            ],
            'Teacher actionsmenu tag with approved enabled' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|Some .*Edit.* .*Delete.* .*disapprove.*{entryid}.*sesskey.*Undo approval.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => false,
                'approved' => true,
            ],
            'Teacher actionsmenu tag with export, approved and showmore enabled' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|Some .*Show more.* .*Edit.* .*Delete.* .*Undo approval.* .*Export to portfolio.* tag|',
                'rolename' => 'editingteacher',
                'enableexport' => true,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => true],
            ],
            'Teacher otherfields tag' => [
                'templatecontent' => 'Some ##otherfields## tag',
                'expected' => '|Some .*{fieldname}.*Example entry.*otherfield.*Another example.* tag|',
                'rolename' => 'editingteacher',
            ],
            'Teacher otherfields tag with some field in the template' => [
                'templatecontent' => 'Some [[myfield]] and ##otherfields## tag',
                'expected' => '|Some .*Example entry.* and .*otherfield.*Another example.* tag|',
                'rolename' => 'editingteacher',
            ],
            // Student scenarios.
            'Student id tag' => [
                'templatecontent' => 'Some ##id## tag',
                'expected' => '|Some {entryid} tag|',
                'rolename' => 'student',
            ],
            'Student delete tag' => [
                'templatecontent' => 'Some ##delete## tag',
                'expected' => '|Some .*delete.*{entryid}.*sesskey.*Delete.* tag|',
                'rolename' => 'student',
            ],
            'Student delete tag on other author entry' => [
                'templatecontent' => 'Some ##delete## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => [],
                'otherauthor' => true,
            ],
            'Student edit tag' => [
                'templatecontent' => 'Some ##edit## tag',
                'expected' => '|Some .*edit.*{entryid}.*sesskey.*Edit.* tag|',
                'rolename' => 'student',
            ],
            'Student edit tag on other author entry' => [
                'templatecontent' => 'Some ##edit## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => [],
                'otherauthor' => true,
            ],
            'Student more tag' => [
                'templatecontent' => 'Some ##more## tag',
                'expected' => '|Some .*more.*{cmid}.*rid.*{entryid}.*More.* tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => true],
            ],
            'Student more tag with showmore set to false' => [
                'templatecontent' => 'Some ##more## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => false],
            ],
            'Student moreurl tag' => [
                'templatecontent' => 'Some ##moreurl## tag',
                'expected' => '|Some .*/mod/data/view.*{cmid}.*rid.*{entryid}.* tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => true],
            ],
            'Student moreurl tag with showmore set to false' => [
                'templatecontent' => 'Some ##moreurl## tag',
                'expected' => '|Some .*/mod/data/view.*{cmid}.*rid.*{entryid}.* tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => false],
            ],
            'Student delcheck tag' => [
                'templatecontent' => 'Some ##delcheck## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
            ],
            'Student user tag' => [
                'templatecontent' => 'Some ##user## tag',
                'expected' => '|Some .*user/view.*{authorid}.*course.*{courseid}.*{authorfullname}.* tag|',
                'rolename' => 'student',
            ],
            'Student userpicture tag' => [
                'templatecontent' => 'Some ##userpicture## tag',
                'expected' => '|Some .*user/view.*{authorid}.*course.*{courseid}.* tag|',
                'rolename' => 'student',
            ],
            'Student export tag' => [
                'templatecontent' => 'Some ##export## tag',
                'expected' => '|Some .*portfolio/add.* tag|',
                'rolename' => 'student',
                'enableexport' => true,
            ],
            'Student export tag not configured' => [
                'templatecontent' => 'Some ##export## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
            ],
            'Student export tag on other user entry' => [
                'templatecontent' => 'Some ##export## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => [],
                'otherauthor' => true,
            ],
            'Student timeadded tag' => [
                'templatecontent' => 'Some ##timeadded## tag',
                'expected' => '|Some <span.*>{timeadded}</span> tag|',
                'rolename' => 'student',
            ],
            'Student timemodified tag' => [
                'templatecontent' => 'Some ##timemodified## tag',
                'expected' => '|Some <span.*>{timemodified}</span> tag|',
                'rolename' => 'student',
            ],
            'Student approve tag approved entry' => [
                'templatecontent' => 'Some ##approve## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
            ],
            'Student approve tag disapproved entry' => [
                'templatecontent' => 'Some ##approve## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => false,
            ],
            'Student disapprove tag approved entry' => [
                'templatecontent' => 'Some ##disapprove## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
            ],
            'Student disapprove tag disapproved entry' => [
                'templatecontent' => 'Some ##disapprove## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => false,
            ],
            'Student approvalstatus tag approved entry' => [
                'templatecontent' => 'Some ##approvalstatus## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
            ],
            'Student approvalstatus tag disapproved entry' => [
                'templatecontent' => 'Some ##approvalstatus## tag',
                'expected' => '|Some .*Pending approval.* tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => false,
            ],
            'Student approvalstatusclass tag approved entry' => [
                'templatecontent' => 'Some ##approvalstatusclass## tag',
                'expected' => '|Some approved tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
            ],
            'Student approvalstatusclass tag disapproved entry' => [
                'templatecontent' => 'Some ##approvalstatusclass## tag',
                'expected' => '|Some notapproved tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => false,
            ],
            'Student tags tag' => [
                'templatecontent' => 'Some ##tags## tag',
                'expected' => '|Some .*Cats.* tag|',
                'rolename' => 'student',
            ],
            'Student field name tag' => [
                'templatecontent' => 'Some [[myfield]] tag',
                'expected' => '|Some .*Example entry.* tag|',
                'rolename' => 'student',
            ],
            'Student field#id name tag' => [
                'templatecontent' => 'Some [[myfield#id]] tag',
                'expected' => '|Some {fieldid} tag|',
                'rolename' => 'student',
            ],
            'Student field#name tag' => [
                'templatecontent' => 'Some [[myfield#name]] tag',
                'expected' => '|Some {fieldname} tag|',
                'rolename' => 'student',
            ],
            'Student field#description tag' => [
                'templatecontent' => 'Some [[myfield#description]] tag',
                'expected' => '|Some {fielddescription} tag|',
                'rolename' => 'student',
            ],
            'Student comments name tag with comments enabled' => [
                'templatecontent' => 'Some ##comments## tag',
                'expected' => '|Some .*Comments.* tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => true,
            ],
            'Student comments name tag with comments disabled' => [
                'templatecontent' => 'Some ##comments## tag',
                'expected' => '|Some  tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
            ],
            'Student comment forced with comments enables' => [
                'templatecontent' => 'No tags',
                'expected' => '|No tags.*Comments.*|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => true,
                'enableratings' => false,
                'options' => ['comments' => true]
            ],
            'Student comment forced without comments enables' => [
                'templatecontent' => 'No tags',
                'expected' => '|^No tags$|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['comments' => true]
            ],
            'Student adding ratings without ratings configured' => [
                'templatecontent' => 'No tags',
                'expected' => '|^No tags$|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['ratings' => true]
            ],
            'Student adding ratings with ratings configured' => [
                'templatecontent' => 'No tags',
                'expected' => '|^No tags$|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => true,
                'options' => ['ratings' => true]
            ],
            'Student actionsmenu tag with default options' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|Some .*edit.*{entryid}.*Edit.* .*delete.*{entryid}.*Delete.* tag|',
                'rolename' => 'student',
            ],
            'Student actionsmenu tag with default options (check Show more is not there)' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|^Some((?!Show more).)*tag$|',
                'rolename' => 'student',
            ],
            'Student actionsmenu tag with show more enabled' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|Some .*view.*{cmid}.*rid.*{entryid}.*Show more.* .*Edit.* .*Delete.* tag|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => false,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => true],
            ],
            'Student actionsmenu tag with export enabled' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|Some .*Edit.* .*Delete.* .*portfolio/add.* tag|',
                'rolename' => 'student',
                'enableexport' => true,
            ],
            'Student actionsmenu tag with approved enabled' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|^Some((?!Approve).)*tag$|',
                'rolename' => 'student',
                'enableexport' => false,
                'approved' => true,
            ],
            'Student actionsmenu tag with export, approved and showmore enabled' => [
                'templatecontent' => 'Some ##actionsmenu## tag',
                'expected' => '|Some .*Show more.* .*Edit.* .*Delete.* .*Export to portfolio.* tag|',
                'rolename' => 'student',
                'enableexport' => true,
                'approved' => true,
                'enablecomments' => false,
                'enableratings' => false,
                'options' => ['showmore' => true],
            ],
            'Student otherfields tag' => [
                'templatecontent' => 'Some ##otherfields## tag',
                'expected' => '|Some .*{fieldname}.*Example entry.*otherfield.*Another example.* tag|',
                'rolename' => 'student',
            ],
            'Student otherfields tag with some field in the template' => [
                'templatecontent' => 'Some [[myfield]] and ##otherfields## tag',
                'expected' => '|Some .*Example entry.* and .*otherfield.*Another example.* tag|',
                'rolename' => 'student',
            ],
        ];
    }

    /**
     * Create all the necessary data to enable portfolio export in mod_data
     *
     * @param stdClass $user the current user record.
     */
    protected function enable_portfolio(stdClass $user) {
        global $DB;
        set_config('enableportfolios', 1);

        $plugin = 'download';
        $name = 'Download';

        $portfolioinstance = (object) [
            'plugin' => $plugin,
            'name' => $name,
            'visible' => 1
        ];
        $portfolioinstance->id = $DB->insert_record('portfolio_instance', $portfolioinstance);
        $userinstance = (object) [
            'instance' => $portfolioinstance->id,
            'userid' => $user->id,
            'name' => 'visible',
            'value' => 1
        ];
        $DB->insert_record('portfolio_instance_user', $userinstance);

        $DB->insert_record('portfolio_log', [
            'portfolio' => $portfolioinstance->id,
            'userid' => $user->id,
            'caller_class' => 'data_portfolio_caller',
            'caller_component' => 'mod_data',
            'time' => time(),
        ]);
    }

    /**
     * Enable the ratings on the database entries.
     *
     * @param context_module $context the activity context
     * @param stdClass $activity the activity record
     * @param array $entries database entries
     * @param stdClass $user the current user record
     * @return stdClass the entries with the rating attribute
     */
    protected function enable_ratings(context_module $context, stdClass $activity, array $entries, stdClass $user) {
        global $CFG;
        $ratingoptions = (object)[
            'context' => $context,
            'component' => 'mod_data',
            'ratingarea' => 'entry',
            'items' => $entries,
            'aggregate' => RATING_AGGREGATE_AVERAGE,
            'scaleid' => $activity->scale,
            'userid' => $user->id,
            'returnurl' => $CFG->wwwroot . '/mod/data/view.php',
            'assesstimestart' => $activity->assesstimestart,
            'assesstimefinish' => $activity->assesstimefinish,
        ];
        $rm = new rating_manager();
        return $rm->get_ratings($ratingoptions);
    }

    /**
     * Test parse add entry template parsing.
     *
     * @covers ::parse_add_entry
     * @dataProvider parse_add_entry_provider
     * @param string $templatecontent the template string
     * @param string $expected expected output
     * @param bool $newentry if it is a new entry or editing and existing one
     */
    public function test_parse_add_entry(
        string $templatecontent,
        string $expected,
        bool $newentry = false
    ): void {
        global $DB, $PAGE;
        // Comments, tags, approval, user role.
        $this->resetAfterTest();

        $params = ['approval' => true];

        $course = $this->getDataGenerator()->create_course();
        $params['course'] = $course;
        $activity = $this->getDataGenerator()->create_module('data', $params);
        $author = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        // Generate an entry.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $fieldrecord = (object)[
            'name' => 'myfield',
            'type' => 'text',
            'description' => 'This is a field'
        ];
        $field = $generator->create_field($fieldrecord, $activity);
        $otherfieldrecord = (object)['name' => 'otherfield', 'type' => 'text'];
        $otherfield = $generator->create_field($otherfieldrecord, $activity);

        if ($newentry) {
            $entryid = null;
            $entry = null;
        } else {
            $entryid = $generator->create_entry(
                $activity,
                [
                    $field->field->id => 'Example entry',
                    $otherfield->field->id => 'Another example',
                ],
                0,
                ['Cats', 'Dogs']
            );
            $entry = (object)[
                'd' => $activity->id,
                'rid' => $entryid,
                "field_{$field->field->id}" => "New value",
                "field_{$otherfield->field->id}" => "Altered value",
            ];
        }

        $manager = manager::create_from_instance($activity);

        // Some cooked variables for the regular expression.
        $replace = [
            '{fieldid}' => $field->field->id,
            '{fieldname}' => $field->field->name,
            '{fielddescription}' => $field->field->description,
            '{otherid}' => $otherfield->field->id,
        ];

        $processdata = (object)[
            'generalnotifications' => ['GENERAL'],
            'fieldnotifications' => [
                $field->field->name => ['FIELD'],
                $otherfield->field->name => ['OTHERFIELD'],
            ],
        ];

        $parser = new template($manager, $templatecontent);
        $result = $parser->parse_add_entry($processdata, $entryid, $entry);

        // We don't want line breaks for the validations.
        $result = str_replace("\n", '', $result);
        $regexp = str_replace(array_keys($replace), array_values($replace), $expected);
        $this->assertMatchesRegularExpression($regexp, $result);
    }

    /**
     * Data provider for test_parse_add_entry().
     *
     * @return array of scenarios
     */
    public static function parse_add_entry_provider(): array {
        return [
            // Editing an entry.
            'Teacher editing entry tags tag' => [
                'templatecontent' => 'Some ##tags## tag',
                'expected' => '|GENERAL.*Some .*select .*tags.*Cats.* tag|',
                'newentry' => false,
            ],
            'Teacher editing entry field name tag' => [
                'templatecontent' => 'Some [[myfield]] tag',
                'expected' => '|GENERAL.*Some .*FIELD.*field_{fieldid}.*input.*New value.* tag|',
                'newentry' => false,
            ],
            'Teacher editing entry field#id tag' => [
                'templatecontent' => 'Some [[myfield#id]] tag',
                'expected' => '|GENERAL.*Some field_{fieldid} tag|',
                'newentry' => false,
            ],
            'Teacher editing field#name tag' => [
                'templatecontent' => 'Some [[myfield#name]] tag',
                'expected' => '|GENERAL.*Some {fieldname} tag|',
                'newentry' => false,
            ],
            'Teacher editing field#description tag' => [
                'templatecontent' => 'Some [[myfield#description]] tag',
                'expected' => '|GENERAL.*Some {fielddescription} tag|',
                'newentry' => false,
            ],
            'Teacher editing entry field otherfields tag' => [
                'templatecontent' => 'Some [[myfield]] and ##otherfields## tag',
                'expected' => '|GENERAL.*Some .*FIELD.*field_{fieldid}.*input.*New value.* '
                              . 'and .*OTHERFIELD.*field_{otherid}.*input.*Altered value.* tag|',
                'newentry' => false,
            ],
            // New entry.
            'Teacher new entry tags tag' => [
                'templatecontent' => 'Some ##tags## tag',
                'expected' => '|GENERAL.*Some .*select .*tags\[\].* tag|',
                'newentry' => true,
            ],
            'Teacher new entry field name tag' => [
                'templatecontent' => 'Some [[myfield]] tag',
                'expected' => '|GENERAL.*Some .*FIELD.*field_{fieldid}.*input.*value="".* tag|',
                'newentry' => true,
            ],
            'Teacher new entry field#id name tag' => [
                'templatecontent' => 'Some [[myfield#id]] tag',
                'expected' => '|GENERAL.*Some field_{fieldid} tag|',
                'newentry' => true,
            ],
            'Teacher new entry field#name tag' => [
                'templatecontent' => 'Some [[myfield#name]] tag',
                'expected' => '|GENERAL.*Some {fieldname} tag|',
                'newentry' => false,
            ],
            'Teacher new entry field#description tag' => [
                'templatecontent' => 'Some [[myfield#description]] tag',
                'expected' => '|GENERAL.*Some {fielddescription} tag|',
                'newentry' => false,
            ],
            'Teacher new entry field otherfields tag' => [
                'templatecontent' => 'Some [[myfield]] and ##otherfields## tag',
                'expected' => '|GENERAL.*Some .*FIELD.*field_{fieldid}.*input.*New value.* '
                              . '.* and .*OTHERFIELD.*field_{otherid}.*input.*Altered value.* |',
                'newentry' => false,
            ],
        ];
    }
}
