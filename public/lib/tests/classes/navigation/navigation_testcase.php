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

namespace core\tests\navigation;

use core\navigation\navigation_node;
use core\output\pix_icon;
use core\url;

/**
 * TODO describe file navigation_testcase
 *
 * @package    core
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class navigation_testcase extends \advanced_testcase {
    protected function setup_node(): navigation_node { // phpcs:ignore
        global $PAGE, $SITE;

        // Perform a reset between tests to reset the PAGE.
        $this->resetAfterTest();

        $PAGE->set_url('/');
        $PAGE->set_course($SITE);

        $activeurl = $PAGE->url;
        $inactiveurl = new url('http://www.moodle.com/');

        navigation_node::override_active_url($PAGE->url);

        $node = new navigation_node('Test Node');
        $node->type = navigation_node::TYPE_SYSTEM;
        // We add the first child without key. This way we make sure all keys search by comparison is performed using ===.
        $node->add('first child without key', null, navigation_node::TYPE_CUSTOM);
        $demo1 = $node->add('demo1', $inactiveurl, navigation_node::TYPE_COURSE, null, 'demo1', new pix_icon('i/course', ''));
        $demo2 = $node->add('demo2', $inactiveurl, navigation_node::TYPE_COURSE, null, 'demo2', new pix_icon('i/course', ''));
        $demo3 = $node->add('demo3', $inactiveurl, navigation_node::TYPE_CATEGORY, null, 'demo3', new pix_icon('i/course', ''));
        $demo4 = $demo3->add('demo4', $inactiveurl, navigation_node::TYPE_COURSE, null, 'demo4', new pix_icon('i/course', ''));
        $demo5 = $demo3->add('demo5', $activeurl, navigation_node::TYPE_COURSE, null, 'demo5', new pix_icon('i/course', ''));
        $demo5->add('activity1', null, navigation_node::TYPE_ACTIVITY, null, 'activity1')->make_active();
        $demo6 = $demo3->add('demo6', null, navigation_node::TYPE_CONTAINER, 'container node test', 'demo6');
        $hiddendemo1 = $node->add(
            'hiddendemo1',
            $inactiveurl,
            navigation_node::TYPE_CATEGORY,
            null,
            'hiddendemo1',
            new pix_icon('i/course', '')
        );
        $hiddendemo1->hidden = true;
        $hiddendemo1->add(
            'hiddendemo2',
            $inactiveurl,
            navigation_node::TYPE_COURSE,
            null,
            'hiddendemo2',
            new pix_icon('i/course', '')
        )->helpbutton = 'Here is a help button';
        $hiddendemo1->add(
            'hiddendemo3',
            $inactiveurl,
            navigation_node::TYPE_COURSE,
            null,
            'hiddendemo3',
            new pix_icon('i/course', '')
        )->display = false;

        return $node;
    }
}
