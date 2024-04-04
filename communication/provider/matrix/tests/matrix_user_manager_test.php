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

namespace communication_matrix;

use moodle_exception;

/**
 * Class matrix_user_manager_test to test the matrix user manager.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Stevani Andolo <stevani.andolo@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \communication_matrix\matrix_user_manager
 */
class matrix_user_manager_test extends \advanced_testcase {
    /**
     * Test fetcihing a users matrix userid from Moodle.
     */
    public function test_get_matrixid_from_moodle_without_field(): void {
        $user = get_admin();

        $this->assertNull(matrix_user_manager::get_matrixid_from_moodle($user->id));
    }

    /**
     * Test fetching a user's matrix userid from Moodle.
     */
    public function test_get_matrixid_from_moodle(): void {
        $this->resetAfterTest();

        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();

        // Add user ids to both users.
        matrix_user_manager::set_matrix_userid_in_moodle(
            $user1->id,
            '@someexampleuser:matrix.moodle.org',
        );

        matrix_user_manager::set_matrix_userid_in_moodle(
            $user2->id,
            '@someotherexampleuser:matrix.moodle.org',
        );

        // And confirm that they're fetched back.
        $this->assertEquals(
            '@someexampleuser:matrix.moodle.org',
            matrix_user_manager::get_matrixid_from_moodle($user1->id),
        );
        $this->assertEquals(
            '@someotherexampleuser:matrix.moodle.org',
            matrix_user_manager::get_matrixid_from_moodle($user2->id),
        );
    }

    /**
     * Test fetching a formatted matrix userid from Moodle when no server is set.
     */
    public function test_get_formatted_matrix_userid_unset(): void {
        $this->expectException(moodle_exception::class);

        matrix_user_manager::get_formatted_matrix_userid('No value');
    }

    /**
     * Test fetch of a formatted matrix userid.
     *
     * @dataProvider get_formatted_matrix_userid_provider
     * @param string $server
     * @param string $username The moodle username to turn into a Matrix username
     * @param string $expecteduserid The expected matrix user id
     */
    public function test_get_formatted_matrix_userid(
        string $server,
        string $username,
        string $expecteduserid,
    ): void {
        $this->resetAfterTest();

        set_config('matrixhomeserverurl', $server, 'communication_matrix');
        $this->assertEquals(
            $expecteduserid,
            matrix_user_manager::get_formatted_matrix_userid($username),
        );
    }

    /**
     * Data provider for get_formatted_matrix_userid.
     *
     * @return array
     */
    public static function get_formatted_matrix_userid_provider(): array {
        return [
            'alphanumeric' => [
                'https://matrix.example.org',
                'alphabet1',
                '@alphabet1:matrix.example.org',
            ],
            'chara' => [
                'https://matrix.example.org',
                'asdf#$%^&*()+{}|<>?!,asdf',
                '@asdf.................asdf:matrix.example.org',
            ],
            'local server' => [
                'https://synapse',
                'colin.creavey',
                '@colin.creavey:synapse',
            ],
            'server with port' => [
                'https://matrix.example.org:8448',
                'colin.creavey',
                '@colin.creavey:matrix.example.org',
            ],
            'numeric username' => [
                'https://matrix.example.org',
                '123456',
                '@' . matrix_user_manager::MATRIX_USER_PREFIX . '123456:matrix.example.org',
            ],
        ];
    }

    /**
     * Data provider for set_matrix_userid_in_moodle.
     *
     * @return array
     */
    public static function set_matrix_userid_in_moodle_provider(): array {
        return array_combine(
            array_keys(self::get_formatted_matrix_userid_provider()),
            array_map(
                fn($value) => [$value[2]],
                self::get_formatted_matrix_userid_provider(),
            ),
        );
    }

    /**
     * Test setting of a user's matrix userid in Moodle.
     *
     * @dataProvider set_matrix_userid_in_moodle_provider
     * @param string $expectedusername
     */
    public function test_set_matrix_userid_in_moodle(
        string $expectedusername,
    ): void {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        matrix_user_manager::set_matrix_userid_in_moodle($user->id, $expectedusername);

        // Get created matrixuserid from moodle.
        $this->assertEquals(
            $expectedusername,
            matrix_user_manager::get_matrixid_from_moodle($user->id),
        );
    }

    /**
     * Test for getting a formatted matrix home server id.
     *
     * @dataProvider get_formatted_matrix_home_server_provider
     * @param string $input
     * @param string $expectedoutput
     */
    public function test_get_formatted_matrix_home_server(
        string $input,
        string $expectedoutput
    ): void {
        $this->resetAfterTest();

        set_config(
            'matrixhomeserverurl',
            $input,
            'communication_matrix',
        );

        $this->assertEquals(
            $expectedoutput,
            matrix_user_manager::get_formatted_matrix_home_server(),
        );
    }

    /**
     * Data provider for get_formatted_matrix_home_server.
     *
     * @return array
     */
    public static function get_formatted_matrix_home_server_provider(): array {
        return [
            'www is removed' => [
                'https://www.example.org',
                'example.org',
            ],
            'www is not removed if it is not at the beginning' => [
                'https://matrix.www.example.org',
                'matrix.www.example.org',
            ],
            'others are not removed' => [
                'https://matrix.example.org',
                'matrix.example.org',
            ],
        ];
    }

    /**
     * Test creation of matrix user profile fields.
     */
    public function test_create_matrix_user_profile_fields(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/user/profile/lib.php");

        $this->resetAfterTest();

        $matrixprofilefield = get_config('communication_matrix', 'matrixuserid_field');
        $this->assertFalse($matrixprofilefield);

        $this->assertIsString(matrix_user_manager::create_matrix_user_profile_fields());
        $matrixprofilefield = get_config('communication_matrix', 'matrixuserid_field');
        $this->assertNotFalse($matrixprofilefield);

        $user = $this->getDataGenerator()->create_user();
        $this->assertObjectHasProperty($matrixprofilefield, profile_user_record($user->id));
    }
}
