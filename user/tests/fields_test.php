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

namespace core_user;

/**
 * Unit tests for \core_user\fields
 *
 * @package core
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \core_user\fields
 */
class fields_test extends \advanced_testcase {

    /**
     * Tests getting the user picture fields.
     */
    public function test_get_picture_fields(): void {
        $this->assertEquals(['id', 'picture', 'firstname', 'lastname', 'firstnamephonetic',
                'lastnamephonetic', 'middlename', 'alternatename', 'imagealt', 'email'],
                fields::get_picture_fields());
    }

    /**
     * Tests getting the user name fields.
     */
    public function test_get_name_fields(): void {
        $this->assertEquals(['firstnamephonetic', 'lastnamephonetic', 'middlename', 'alternatename',
                'firstname', 'lastname'],
                fields::get_name_fields());

        $this->assertEquals(['firstname', 'lastname',
                'firstnamephonetic', 'lastnamephonetic', 'middlename', 'alternatename'],
                fields::get_name_fields(true));
    }

    /**
     * Tests getting the identity fields.
     */
    public function test_get_identity_fields(): void {
        global $DB, $CFG, $COURSE;

        $this->resetAfterTest();

        require_once($CFG->dirroot . '/user/profile/lib.php');

        // Create custom profile fields, one with each visibility option.
        $generator = self::getDataGenerator();
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'a', 'name' => 'A',
                'visible' => PROFILE_VISIBLE_ALL]);
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'b', 'name' => 'B',
                'visible' => PROFILE_VISIBLE_PRIVATE]);
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'c', 'name' => 'C',
                'visible' => PROFILE_VISIBLE_NONE]);
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'd', 'name' => 'D',
                'visible' => PROFILE_VISIBLE_TEACHERS]);

        // Set the extra user fields to include email, department, and all custom profile fields.
        set_config('showuseridentity', 'email,department,profile_field_a,profile_field_b,' .
                'profile_field_c,profile_field_d');
        set_config('hiddenuserfields', 'email');

        // Create a test course and a student in the course.
        $course = $generator->create_course();
        $coursecontext = \context_course::instance($course->id);
        $user = $generator->create_user();
        $anotheruser = $generator->create_user();
        $usercontext = \context_user::instance($anotheruser->id);
        $generator->enrol_user($user->id, $course->id, 'student');

        // When no context is provided, it does no access checks and should return all specified (other than non-visible).
        $this->assertEquals(['email', 'department', 'profile_field_a', 'profile_field_b', 'profile_field_d'],
                fields::get_identity_fields(null));

        // If you turn off custom profile fields, you don't get those.
        $this->assertEquals(['email', 'department'], fields::get_identity_fields(null, false));

        // Request in context as an administator.
        $this->setAdminUser();
        $this->assertEquals(['email', 'department', 'profile_field_a', 'profile_field_b',
                'profile_field_c', 'profile_field_d'],
                fields::get_identity_fields($coursecontext));
        $this->assertEquals(['email', 'department'],
                fields::get_identity_fields($coursecontext, false));

        // Request in context as a student - they don't have any of the capabilities to see identity
        // fields or profile fields.
        $this->setUser($user);
        $this->assertEquals([], fields::get_identity_fields($coursecontext));

        // Give the student the basic identity fields permission (also makes them count as 'teacher'
        // for the teacher-restricted field).
        $COURSE = $course; // Horrible hack, because PROFILE_VISIBLE_TEACHERS relies on this global.
        $roleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
        role_change_permission($roleid, $coursecontext, 'moodle/site:viewuseridentity', CAP_ALLOW);
        $this->assertEquals(['department', 'profile_field_a', 'profile_field_d'],
                fields::get_identity_fields($coursecontext));
        $this->assertEquals(['department'],
                fields::get_identity_fields($coursecontext, false));

        // Give them permission to view hidden user fields.
        role_change_permission($roleid, $coursecontext, 'moodle/course:viewhiddenuserfields', CAP_ALLOW);
        $this->assertEquals(['email', 'department', 'profile_field_a', 'profile_field_d'],
                fields::get_identity_fields($coursecontext));
        $this->assertEquals(['email', 'department'],
                fields::get_identity_fields($coursecontext, false));

        // Also give them permission to view all profile fields.
        role_change_permission($roleid, $coursecontext, 'moodle/user:viewalldetails', CAP_ALLOW);
        $this->assertEquals(['email', 'department', 'profile_field_a', 'profile_field_b',
                'profile_field_c', 'profile_field_d'],
                fields::get_identity_fields($coursecontext));
        $this->assertEquals(['email', 'department'],
                fields::get_identity_fields($coursecontext, false));

        // Even if we give them student role in the user context they can't view anything...
        $generator->role_assign($roleid, $user->id, $usercontext->id);
        $this->assertEquals([], fields::get_identity_fields($usercontext));

        // Give them basic permission.
        role_change_permission($roleid, $usercontext, 'moodle/site:viewuseridentity', CAP_ALLOW);
        $this->assertEquals(['department', 'profile_field_a', 'profile_field_d'],
                fields::get_identity_fields($usercontext));
        $this->assertEquals(['department'],
                fields::get_identity_fields($usercontext, false));

        // Give them the hidden user fields permission (it's a different one).
        role_change_permission($roleid, $usercontext, 'moodle/user:viewhiddendetails', CAP_ALLOW);
        $this->assertEquals(['email', 'department', 'profile_field_a', 'profile_field_d'],
                fields::get_identity_fields($usercontext));
        $this->assertEquals(['email', 'department'],
                fields::get_identity_fields($usercontext, false));

        // Also give them permission to view all profile fields.
        role_change_permission($roleid, $usercontext, 'moodle/user:viewalldetails', CAP_ALLOW);
        $this->assertEquals(['email', 'department', 'profile_field_a', 'profile_field_b',
                'profile_field_c', 'profile_field_d'],
                fields::get_identity_fields($usercontext));
        $this->assertEquals(['email', 'department'],
                fields::get_identity_fields($usercontext, false));
    }

    /**
     * Test getting identity fields, when one of them refers to a non-existing custom profile field
     */
    public function test_get_identity_fields_invalid(): void {
        $this->resetAfterTest();

        $this->getDataGenerator()->create_custom_profile_field([
            'datatype' => 'text',
            'shortname' => 'real',
            'name' => 'I\'m real',
        ]);

        // The "fake" profile field does not exist.
        set_config('showuseridentity', 'email,profile_field_real,profile_field_fake');

        $this->assertEquals([
            'email',
            'profile_field_real',
        ], fields::get_identity_fields(null));
    }

    /**
     * Tests the get_required_fields function.
     *
     * This function composes the results of get_identity/name/picture_fields, so we are not going
     * to test the details of the identity permissions as that was already covered. Just how they
     * are included/combined.
     */
    public function test_get_required_fields(): void {
        $this->resetAfterTest();

        // Set up some profile fields.
        $generator = self::getDataGenerator();
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'a', 'name' => 'A']);
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'b', 'name' => 'B']);
        set_config('showuseridentity', 'email,department,profile_field_a');

        // What happens if you don't ask for anything?
        $fields = fields::empty();
        $this->assertEquals([], $fields->get_required_fields());

        // Try each invidual purpose.
        $fields = fields::for_identity(null);
        $this->assertEquals(['email', 'department', 'profile_field_a'], $fields->get_required_fields());
        $fields = fields::for_userpic();
        $this->assertEquals(fields::get_picture_fields(), $fields->get_required_fields());
        $fields = fields::for_name();
        $this->assertEquals(fields::get_name_fields(), $fields->get_required_fields());

        // Try combining them all. There should be no duplicates (e.g. email), and the 'id' field
        // should be moved to the start.
        $fields = fields::for_identity(null)->with_name()->with_userpic();
        $this->assertEquals(['id', 'email', 'department', 'profile_field_a', 'picture',
                'firstname', 'lastname', 'firstnamephonetic', 'lastnamephonetic', 'middlename',
                'alternatename', 'imagealt'], $fields->get_required_fields());

        // Add some specified fields to a default result.
        $fields = fields::for_identity(null, true)->including('city', 'profile_field_b');
        $this->assertEquals(['email', 'department', 'profile_field_a', 'city', 'profile_field_b'],
                $fields->get_required_fields());

        // Remove some fields, one of which actually is in the list.
        $fields = fields::for_identity(null, true)->excluding('email', 'city');
        $this->assertEquals(['department', 'profile_field_a'], $fields->get_required_fields());

        // Add and remove fields.
        $fields = fields::for_identity(null, true)->including('city', 'profile_field_b')->excluding('city', 'department');
        $this->assertEquals(['email', 'profile_field_a', 'profile_field_b'],
                $fields->get_required_fields());

        // Request the list without profile fields, check that still works with both sources.
        $fields = fields::for_identity(null, false)->including('city', 'profile_field_b')->excluding('city', 'department');
        $this->assertEquals(['email'], $fields->get_required_fields());
    }

    /**
     * Tests the get_required_fields function when you use the $limitpurposes parameter.
     */
    public function test_get_required_fields_limitpurposes(): void {
        $this->resetAfterTest();

        // Set up some profile fields.
        $generator = self::getDataGenerator();
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'a', 'name' => 'A']);
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'b', 'name' => 'B']);
        set_config('showuseridentity', 'email,department,profile_field_a');

        // Create a fields object with all three purposes, plus included and excluded fields.
        $fields = fields::for_identity(null, true)->with_name()->with_userpic()
            ->including('city', 'profile_field_b')->excluding('firstnamephonetic', 'middlename', 'alternatename');

        // Check the result with all purposes.
        $this->assertEquals(['id', 'email', 'department', 'profile_field_a', 'picture',
                'firstname', 'lastname', 'lastnamephonetic', 'imagealt', 'city',
                'profile_field_b'],
                $fields->get_required_fields([fields::PURPOSE_IDENTITY, fields::PURPOSE_NAME,
                fields::PURPOSE_USERPIC, fields::CUSTOM_INCLUDE]));

        // Limit to identity and custom includes.
        $this->assertEquals(['email', 'department', 'profile_field_a', 'city', 'profile_field_b'],
                $fields->get_required_fields([fields::PURPOSE_IDENTITY, fields::CUSTOM_INCLUDE]));

        // Limit to name fields.
        $this->assertEquals(['firstname', 'lastname', 'lastnamephonetic'],
                $fields->get_required_fields([fields::PURPOSE_NAME]));
    }

    /**
     * There should be an exception if you try to 'limit' purposes to one that wasn't even included.
     */
    public function test_get_required_fields_limitpurposes_not_in_constructor(): void {
        $fields = fields::for_identity(null);
        $this->expectExceptionMessage('$limitpurposes can only include purposes defined in object');
        $fields->get_required_fields([fields::PURPOSE_USERPIC]);
    }

    /**
     * Sets up data and a fields object for all the get_sql tests.
     *
     * @return fields Constructed fields object for testing
     */
    protected function init_for_sql_tests(): fields {
        $generator = self::getDataGenerator();
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'a', 'name' => 'A']);
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'b', 'name' => 'B']);

        // Create a couple of users. One doesn't have a profile field set, so we can test that.
        $generator->create_user(['profile_field_a' => 'A1', 'profile_field_b' => 'B1',
                'city' => 'C1', 'department' => 'D1', 'email' => 'e1@example.org',
                'idnumber' => 'XXX1', 'username' => 'u1']);
        $generator->create_user(['profile_field_a' => 'A2',
                'city' => 'C2', 'department' => 'D2', 'email' => 'e2@example.org',
                'idnumber' => 'XXX2', 'username' => 'u2']);

        // It doesn't matter how we construct it (we already tested get_required_fields which is
        // where all those values are actually used) so let's just list the fields we want manually.
        return fields::empty()->including('department', 'city', 'profile_field_a', 'profile_field_b');
    }

    /**
     * Tests getting SQL (and actually using it).
     */
    public function test_get_sql_variations(): void {
        global $DB;
        $this->resetAfterTest();

        $fields = $this->init_for_sql_tests();
        fields::reset_unique_identifier();

        // Basic SQL.
        ['selects' => $selects, 'joins' => $joins, 'params' => $joinparams, 'mappings' => $mappings] =
                (array)$fields->get_sql();
        $sql = "SELECT idnumber
                       $selects
                  FROM {user}
                       $joins
                 WHERE idnumber LIKE ?
              ORDER BY idnumber";
        $records = $DB->get_records_sql($sql, array_merge($joinparams, ['X%']));
        $this->assertCount(2, $records);
        $expected1 = (object)['profile_field_a' => 'A1', 'profile_field_b' => 'B1',
                'city' => 'C1', 'department' => 'D1', 'idnumber' => 'XXX1'];
        $expected2 = (object)['profile_field_a' => 'A2', 'profile_field_b' => null,
                'city' => 'C2', 'department' => 'D2', 'idnumber' => 'XXX2'];
        $this->assertEquals($expected1, $records['XXX1']);
        $this->assertEquals($expected2, $records['XXX2']);

        $this->assertEquals([
                'department' => '{user}.department',
                'city' => '{user}.city',
                'profile_field_a' => $DB->sql_compare_text('uf1d_1.data', 255),
                'profile_field_b' => $DB->sql_compare_text('uf1d_2.data', 255)], $mappings);

        // SQL using named params.
        ['selects' => $selects, 'joins' => $joins, 'params' => $joinparams] =
                (array)$fields->get_sql('', true);
        $sql = "SELECT idnumber
                       $selects
                  FROM {user}
                       $joins
                 WHERE idnumber LIKE :idnum
              ORDER BY idnumber";
        $records = $DB->get_records_sql($sql, array_merge($joinparams, ['idnum' => 'X%']));
        $this->assertCount(2, $records);
        $this->assertEquals($expected1, $records['XXX1']);
        $this->assertEquals($expected2, $records['XXX2']);

        // SQL using alias for user table.
        ['selects' => $selects, 'joins' => $joins, 'params' => $joinparams, 'mappings' => $mappings] =
                (array)$fields->get_sql('u');
        $sql = "SELECT idnumber
                       $selects
                  FROM {user} u
                       $joins
                 WHERE idnumber LIKE ?
              ORDER BY idnumber";
        $records = $DB->get_records_sql($sql, array_merge($joinparams, ['X%']));
        $this->assertCount(2, $records);
        $this->assertEquals($expected1, $records['XXX1']);
        $this->assertEquals($expected2, $records['XXX2']);

        $this->assertEquals([
                'department' => 'u.department',
                'city' => 'u.city',
                'profile_field_a' => $DB->sql_compare_text('uf3d_1.data', 255),
                'profile_field_b' => $DB->sql_compare_text('uf3d_2.data', 255)], $mappings);

        // Returning prefixed fields.
        ['selects' => $selects, 'joins' => $joins, 'params' => $joinparams] =
                (array)$fields->get_sql('', false, 'u_');
        $sql = "SELECT idnumber
                       $selects
                  FROM {user}
                       $joins
                 WHERE idnumber LIKE ?
              ORDER BY idnumber";
        $records = $DB->get_records_sql($sql, array_merge($joinparams, ['X%']));
        $this->assertCount(2, $records);
        $expected1 = (object)['u_profile_field_a' => 'A1', 'u_profile_field_b' => 'B1',
                'u_city' => 'C1', 'u_department' => 'D1', 'idnumber' => 'XXX1'];
        $this->assertEquals($expected1, $records['XXX1']);

        // Renaming the id field. We need to use a different set of fields so it actually has the
        // id field.
        $fields = fields::for_userpic();
        ['selects' => $selects, 'joins' => $joins, 'params' => $joinparams] =
                (array)$fields->get_sql('', false, '', 'userid');
        $sql = "SELECT idnumber
                       $selects
                  FROM {user}
                       $joins
                 WHERE idnumber LIKE ?
              ORDER BY idnumber";
        $records = $DB->get_records_sql($sql, array_merge($joinparams, ['X%']));
        $this->assertCount(2, $records);

        // User id was renamed.
        $this->assertObjectNotHasProperty('id', $records['XXX1']);
        $this->assertObjectHasProperty('userid', $records['XXX1']);

        // Other fields are normal (just try a couple).
        $this->assertObjectHasProperty('firstname', $records['XXX1']);
        $this->assertObjectHasProperty('imagealt', $records['XXX1']);

        // Check the user id is actually right.
        $this->assertEquals('XXX1',
                $DB->get_field('user', 'idnumber', ['id' => $records['XXX1']->userid]));

        // Rename the id field and also use a prefix.
        ['selects' => $selects, 'joins' => $joins, 'params' => $joinparams] =
                (array)$fields->get_sql('', false, 'u_', 'userid');
        $sql = "SELECT idnumber
                       $selects
                  FROM {user}
                       $joins
                 WHERE idnumber LIKE ?
              ORDER BY idnumber";
        $records = $DB->get_records_sql($sql, array_merge($joinparams, ['X%']));
        $this->assertCount(2, $records);

        // User id was renamed.
        $this->assertObjectNotHasProperty('id', $records['XXX1']);
        $this->assertObjectNotHasProperty('u_id', $records['XXX1']);
        $this->assertObjectHasProperty('userid', $records['XXX1']);

        // Other fields are prefixed (just try a couple).
        $this->assertObjectHasProperty('u_firstname', $records['XXX1']);
        $this->assertObjectHasProperty('u_imagealt', $records['XXX1']);

        // Without a leading comma.
        ['selects' => $selects, 'joins' => $joins, 'params' => $joinparams] =
                (array)$fields->get_sql('', false, '', '', false);
        $sql = "SELECT $selects
                  FROM {user}
                       $joins
                 WHERE idnumber LIKE ?
              ORDER BY idnumber";
        $records = $DB->get_records_sql($sql, array_merge($joinparams, ['X%']));
        $this->assertCount(2, $records);
        foreach ($records as $key => $record) {
            // ID should be the first field used by get_records_sql.
            $this->assertEquals($key, $record->id);
            // Check 2 other sample properties.
            $this->assertObjectHasProperty('firstname', $record);
            $this->assertObjectHasProperty('imagealt', $record);
        }
    }

    /**
     * Tests what happens if you use the SQL multiple times in a query (i.e. that it correctly
     * creates the different identifiers).
     */
    public function test_get_sql_multiple(): void {
        global $DB;
        $this->resetAfterTest();

        $fields = $this->init_for_sql_tests();

        // Inner SQL.
        ['selects' => $selects1, 'joins' => $joins1, 'params' => $joinparams1] =
                (array)$fields->get_sql('u1', true);
        // Outer SQL.
        $fields2 = fields::empty()->including('profile_field_a', 'email');
        ['selects' => $selects2, 'joins' => $joins2, 'params' => $joinparams2] =
                (array)$fields2->get_sql('u2', true);

        // Crazy combined query.
        $sql = "SELECT username, details.profile_field_b AS innerb, details.city AS innerc
                       $selects2
                  FROM {user} u2
                       $joins2
             LEFT JOIN (
                          SELECT u1.id
                                 $selects1
                            FROM {user} u1
                                 $joins1
                           WHERE idnumber LIKE :idnum
                       ) details ON details.id = u2.id
              ORDER BY username";
        $records = $DB->get_records_sql($sql, array_merge($joinparams1, $joinparams2, ['idnum' => 'X%']));
        // The left join won't match for admin.
        $this->assertNull($records['admin']->innerb);
        $this->assertNull($records['admin']->innerc);
        // It should match for one of the test users though.
        $expected1 = (object)['username' => 'u1', 'innerb' => 'B1', 'innerc' => 'C1',
                'profile_field_a' => 'A1', 'email' => 'e1@example.org'];
        $this->assertEquals($expected1, $records['u1']);
    }

    /**
     * Tests the get_sql function when there are no fields to retrieve.
     */
    public function test_get_sql_nothing(): void {
        $fields = fields::empty();
        ['selects' => $selects, 'joins' => $joins, 'params' => $joinparams] = (array)$fields->get_sql();
        $this->assertEquals('', $selects);
        $this->assertEquals('', $joins);
        $this->assertEquals([], $joinparams);
    }

    /**
     * Tests get_sql when there are no custom fields; in this scenario, the joins and joinparams
     * are always blank.
     */
    public function test_get_sql_no_custom_fields(): void {
        $fields = fields::empty()->including('city', 'country');
        ['selects' => $selects, 'joins' => $joins, 'params' => $joinparams, 'mappings' => $mappings] =
                (array)$fields->get_sql('u');
        $this->assertEquals(', u.city, u.country', $selects);
        $this->assertEquals('', $joins);
        $this->assertEquals([], $joinparams);
        $this->assertEquals(['city' => 'u.city', 'country' => 'u.country'], $mappings);
    }

    /**
     * Tests the format of the $selects string, which is important particularly for backward
     * compatibility.
     */
    public function test_get_sql_selects_format(): void {
        global $DB;

        $this->resetAfterTest();
        fields::reset_unique_identifier();

        $generator = self::getDataGenerator();
        $generator->create_custom_profile_field(['datatype' => 'text', 'shortname' => 'a', 'name' => 'A']);

        // When we list fields that include custom profile fields...
        $fields = fields::empty()->including('id', 'profile_field_a');

        // Supplying an alias: all fields have alias.
        $selects = $fields->get_sql('u')->selects;
        $this->assertEquals(', u.id, ' . $DB->sql_compare_text('uf1d_1.data', 255) . ' AS profile_field_a', $selects);

        // No alias: all files have {user} because of the joins.
        $selects = $fields->get_sql()->selects;
        $this->assertEquals(', {user}.id, ' . $DB->sql_compare_text('uf2d_1.data', 255) . ' AS profile_field_a', $selects);

        // When the list doesn't include custom profile fields...
        $fields = fields::empty()->including('id', 'city');

        // Supplying an alias: all fields have alias.
        $selects = $fields->get_sql('u')->selects;
        $this->assertEquals(', u.id, u.city', $selects);

        // No alias: fields do not have alias at all.
        $selects = $fields->get_sql()->selects;
        $this->assertEquals(', id, city', $selects);
    }

    /**
     * Data provider for {@see test_get_sql_fullname}
     *
     * @return array
     */
    public static function get_sql_fullname_provider(): array {
        return [
            ['firstname lastname', 'FN LN'],
            ['lastname, firstname', 'LN, FN'],
            ['alternatename \'middlename\' lastname!', 'AN \'MN\' LN!'],
            ['[firstname lastname alternatename]', '[FN LN AN]'],
            ['firstnamephonetic lastnamephonetic', 'FNP LNP'],
            ['firstname alternatename lastname', 'FN AN LN'],
        ];
    }

    /**
     * Test sql_fullname_display method with various fullname formats
     *
     * @param string $fullnamedisplay
     * @param string $expectedfullname
     *
     * @dataProvider get_sql_fullname_provider
     */
    public function test_get_sql_fullname(string $fullnamedisplay, string $expectedfullname): void {
        global $DB;

        $this->resetAfterTest();

        set_config('fullnamedisplay', $fullnamedisplay);
        $user = $this->getDataGenerator()->create_user([
            'firstname' => 'FN',
            'lastname' => 'LN',
            'firstnamephonetic' => 'FNP',
            'lastnamephonetic' => 'LNP',
            'middlename' => 'MN',
            'alternatename' => 'AN',
        ]);

        [$sqlfullname, $params] = fields::get_sql_fullname('u');
        $fullname = $DB->get_field_sql("SELECT {$sqlfullname} FROM {user} u WHERE u.id = :id", $params + [
            'id' => $user->id,
        ]);

        $this->assertEquals($expectedfullname, $fullname);
    }

    /**
     * Test sql_fullname_display when one of the configured name fields is null
     */
    public function test_get_sql_fullname_null_field(): void {
        global $DB;

        $this->resetAfterTest();

        set_config('fullnamedisplay', 'firstname lastname alternatename');
        $user = $this->getDataGenerator()->create_user([
            'firstname' => 'FN',
            'lastname' => 'LN',
        ]);

        // Set alternatename field to null, ensure we still get result in later assertion.
        $user->alternatename = null;
        user_update_user($user, false);

        [$sqlfullname, $params] = fields::get_sql_fullname('u');
        $fullname = $DB->get_field_sql("SELECT {$sqlfullname} FROM {user} u WHERE u.id = :id", $params + [
            'id' => $user->id,
        ]);

        $this->assertEquals('FN LN ', $fullname);
    }
}
