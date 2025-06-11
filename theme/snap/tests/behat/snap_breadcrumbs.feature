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
# Tests for toggle course section visibility in non edit mode in snap.
#
# @package    theme_snap
# @author     Diego Casas
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_breadcrumbs
Feature: Check that the breadcrumbs are being shown correctly.
  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
      | student1 | Student   | 1        | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname  | category  |
      | Course 1 | C1         | 0         |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | admin    | C1 | manager |

  @javascript
  Scenario Outline: Breadcrumbs are displayed correctly when default home page is set as "Site".
    Given the following config values are set as admin:
      | defaulthomepage | <defaulthomepage> |
    And I log in as "<user>"
    And I am on the course main page for "C1"
    And I should see "<homepage>" in the ".breadcrumb" "css_element"
    And I should see "My Courses" in the ".breadcrumb" "css_element"
    Examples:
      | user     | defaulthomepage | homepage  |
      | admin    | 0               | Home      |
      | teacher1 | 0               | Home      |
      | student1 | 0               | Home      |
      | admin    | 1               | Dashboard |
      | teacher1 | 1               | Dashboard |
      | student1 | 1               | Dashboard |
      | admin    | 2               | Dashboard |
      | teacher1 | 2               | Dashboard |
      | student1 | 2               | Dashboard |
