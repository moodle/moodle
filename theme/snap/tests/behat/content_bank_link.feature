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
# Test for content bank link in the front page menu, course admin menu and category settings.
#
# @package    theme_snap
# @copyright  Copyright (c) 2020 Open LMS.
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course @theme_snap_contentbank
Feature: When the Moodle theme is set to Snap, the content bank link should show in front page
  menu, course administration menu and category settings.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | editingteacher |
      | teacher1 | C1     | editingteacher |
    And I assign "teacher1" the role of "editingteacher" in the frontpage

  @javascript
  Scenario: Users can see the content bank link.
    And I log in as "admin"
    And I am on front page
    And I click on "#admin-menu-trigger" "css_element"
    And I should see "Content bank"
    And I follow "My Courses"
    And I click on "Browse all courses" "link"
    And I follow "Manage courses"
    And I click on "#admin-menu-trigger" "css_element"
    And I should see "Content bank"
    And I log out
    And I log in as "teacher1"
    And I am on front page
    And I click on "#admin-menu-trigger" "css_element"
    And I should not see "Content bank"
    And I am on the course main page for "C1"
    And I click on "#admin-menu-trigger" "css_element"
    And I should see "Content bank"
