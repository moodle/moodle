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

/**
 * @package   local_email
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('lib.php');

function test() {
    echo "<hr /><b>Fake user object</b>";
    $user = (object) array('firstname' => 'User', 'lastname' => 'Test',
                           'email' => 'testuser@somewhere.com',
                           'username' => 'testuser', 'newpassword' => 'somenewpassword');
    $sender = (object) array('firstname' => 'Test', 'Lastname' => 'User');
    echo EmailTemplate::send('user_create', array('user' => $user, 'course' => 2,
                             'sender' => $sender));

    echo EmailTemplate::send('user_added_to_course', array('course' => 2),
      array(array('user' => 3), array('user' => 56), array('user' => $user)));

    echo "<hr /><b>Email template from database</b>";
    echo EmailTemplate::send('user_added_to_course', array('user' => 3, 'course' => 2));

    echo "<hr /><b>Email to current user about current course</b>";
    echo EmailTemplate::send('user_added_to_course');

    echo "<hr/><b>Email all users in a department</b>";
    echo EmailTemplate::send_to_all_users_in_department(3, 'user_added_to_course',
                                                        array('course' => 2));
}

test();
