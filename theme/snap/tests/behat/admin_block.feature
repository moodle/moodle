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
# Tests for visibility of admin block by user type and page.
#
# @package    theme_snap
# @copyright Copyright (c) 2015 Open LMS
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap
Feature: When the moodle theme is set to Snap, the admin block will only be shown when appropriate.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | advuser  | Advanced  | 1        | advuser@example.com  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | admin    | C1     | editingteacher |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | teacher        |
      | student1 | C1     | student        |
    And the following "roles" exist:
      | shortname    | name          | archetype |
      | configviewer | Config Viewer |           |
    And the following "permission overrides" exist:
      | capability                    | permission | role           | contextlevel | reference |
      | moodle/site:configview        | Allow      | configviewer   | System       |           |
    And the following "system role assigns" exist:
      | user    | course               | role         |
      | advuser | Acceptance test site | configviewer |

  @javascript
  Scenario Outline: User only sees admin block on relevant pages.
    Given I log in as "<user>"
    # Check site page.
    And I am on site homepage
    Then "#admin-menu-trigger" "css_element" <existssite> exist
    # Check dashboard page.
    When I am on homepage
    Then "#admin-menu-trigger" "css_element" <existsdashboard> exist
    # Check course page.
    When I am on the course main page for "C1"
    Then "#admin-menu-trigger" "css_element" <existscourse> exist
    # Check profile page.
    When I open the user menu
    And I follow "Profile"
    Then "#admin-menu-trigger" "css_element" <existsprofile> exist
    Examples:
      | user     | existssite | existsdashboard | existscourse | existsprofile |
      | student1 | should not | should not      | should not   | should not    |
      | teacher1 | should not | should not      | should       | should not    |
      | teacher2 | should not | should not      | should       | should not    |
      | admin    | should     | should          | should       | should not    |

  @javascript
  Scenario: User with the moodle/site:configview capability should be able to see the snap gear icon.
    Given I log in as "advuser"
    And I am on site homepage
    Then "#admin-menu-trigger" "css_element" should exist
    Then I log out
    And I log in as "admin"
    And the following "permission overrides" exist:
      | capability                     | permission | role           | contextlevel | reference |
      | moodle/user:update             | Allow      | configviewer   | System       |           |
      | moodle/site:uploadusers        | Allow      | configviewer   | System       |           |
      | moodle/site:viewparticipants   | Allow      | configviewer   | System       |           |
      | moodle/user:create             | Allow      | configviewer   | System       |           |
      | moodle/user:delete             | Allow      | configviewer   | System       |           |
      | moodle/user:editmessageprofile | Allow      | configviewer   | System       |           |
      | moodle/user:editprofile        | Allow      | configviewer   | System       |           |
      | moodle/user:viewalldetails     | Allow      | configviewer   | System       |           |
    And I log out
    When I log in as "advuser"
    And I am on site homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I expand "Site administration" node
    And I should see "Users"
    And I expand "Users" node
    And I should see "Accounts"
    Then I log out
    And I log in as "admin"
    And the following "permission overrides" exist:
      | capability                    | permission    | role           | contextlevel | reference |
      | moodle/site:configview        | Prohibit      | configviewer   | System       |           |
    Then I log out
    And I log in as "advuser"
    And I am on site homepage
    Then "#admin-menu-trigger" "css_element" should not exist

  @javascript
  Scenario: Gear icon and admin menu should appear on Category and My account page.
    Given I log in as "admin"
    And I am on site homepage
    And I am on my account default page
    And "#admin-menu-trigger" "css_element" should exist
    And I am on course index
    And "#admin-menu-trigger" "css_element" should exist
