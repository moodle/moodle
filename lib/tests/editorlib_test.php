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

namespace core;

/**
 * Tests editors subsystem.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas (http://dougiamas.com)
 * @author     Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editorlib_test extends \advanced_testcase {

    /**
     * Tests the installation of event handlers from file
     */
    public function test_get_preferred_editor() {

        // Fake a user agent.
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_5; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.21     5 Safari/534.10';

        $enabled = editors_get_enabled();
        // Array assignment is always a clone.
        $editors = $enabled;

        $first = array_shift($enabled);

        // Get the default editor which should be the first in the list.
        set_user_preference('htmleditor', '');
        $preferred = editors_get_preferred_editor();
        $this->assertEquals($first, $preferred);

        foreach ($editors as $key => $editor) {
            // User has set a preference for a specific editor.
            set_user_preference('htmleditor', $key);
            $preferred = editors_get_preferred_editor();
            $this->assertEquals($editor, $preferred);
        }
    }

}

