# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Tests to make sure admin menu is only shown to relevant people on relevant pages.
#
# @package   theme_snap
# @author    Guy Thomas
# @copyright Copyright (c) 2016 Open LMS.
# @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When the moodle theme is set to Snap, students do not see the course admin menu for 'topics',
  'weeks' and 'singleactivity' - for any other format they do.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript
  Scenario Outline: When on main course page, user can / cannot access course admin menu. Students can access menu for
  any format except topics, weeks, and singleactivity. Teachers can access menu for all course formats.
    Given the course format for "C1" is set to "<format>"
    And I log in as "<user>"
    And I am on the course main page for "C1"
    Then "#admin-menu-trigger" "css_element" should <existornot>
    Examples:
      | user     | format         | existornot |
      | student1 | topics         | not exist  |
      | student1 | weeks          | not exist  |
      | student1 | social         | exist      |
      | teacher1 | topics         | exist      |
      | teacher1 | weeks          | exist      |
      | teacher1 | social         | exist      |

  @javascript
  Scenario Outline: When on main course page, user can / cannot access course admin menu. Students can't access menu for
  singleactivity. Teachers can access menu for all course formats.
    Given the course format for "C1" is set to "singleactivity"
    And I log in as "admin"
    And I am on the course main page for "C1"
    And I set the following fields to these values:
      | Forum name | Single Forum Course |
    And I press "Save and display"
    And I log out
    And I log in as "<user>"
    And I am on the course main page for "C1"
    Then "#admin-menu-trigger" "css_element" should <existornot>
    Examples:
      | user     | existornot |
      | student1 | not exist  |
      | teacher1 | exist      |

  @javascript
  Scenario Outline: When not on main course page, user can / cannot access course admin menu. Students cannot
  access menu for any format. Teacher can access menu for all course formats.
    Given the course format for "C1" is set to "<format>"
    And I log in as "<user>"
    And I am on the course "resources" page for "C1"
    Then "#admin-menu-trigger" "css_element" should <existornot>
    Examples:
      | user     | format         | existornot |
      | student1 | topics         | not exist  |
      | student1 | weeks          | not exist  |
      | student1 | singleactivity | not exist  |
      | student1 | social         | not exist  |
      | teacher1 | topics         | exist      |
      | teacher1 | weeks          | exist      |
      | teacher1 | singleactivity | exist      |
      | teacher1 | social         | exist      |
