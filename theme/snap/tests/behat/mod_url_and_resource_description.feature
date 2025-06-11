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
# Test for intermediate page in file and url activities
#
# @package    theme_snap
# @author     Fabian Batioja <fabian.batioja@openlms.net>
# @copyright  Copyright (c) 2019 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @_file_upload
Feature: When the moodle theme is set to Snap, the users see an intermediate page to display the description in mod_url and mod_resource.

  Background:
    Given the following config values are set as admin:
      | theme           | snap |
    And the following config values are set as admin:
      | displayoptions     | 0,1,2,3,4,5,6 | url        |
      | resourcedisplay    | card          | theme_snap |
      | displaydescription | 1             | theme_snap |
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity   | name         | intro                       | course | idnumber  | section | showdescription | printintro |
      | resource   | Resource 1   | Test resource description   | C1     | resource1 | 0       | 1               | 1          |

  Scenario: As a teacher I should see an intermediate page with the description in mod_resource.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I click on ".modtype_resource a.mod-link" "css_element"
    Then I should see "Test resource description"
    And "resource1.txt" "link" should exist
    And the following config values are set as admin:
      | resourcedisplay | list | theme_snap |
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I click on ".modtype_resource a.mod-link" "css_element"
    And I should not see "Test resource description"
    And "resource1.txt" "link" should not exist

  @javascript @_switch_window
  Scenario Outline: Add a URL and ensure it is displayed correctly.
    Given the following "activity" exists:
      | activity       | url                 |
      | course         | C1                  |
      | idnumber       | url1                |
      | name           | Url 1               |
      | intro          | URL description     |
      | externalurl    | https://moodle.org/ |
      | display        | <display>           |
      | popupwidth     | 620                 |
      | popupheight    | 450                 |
      | printintro     | 1                   |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I wait until the page is ready
    And I click on ".modtype_url a.mod-link" "css_element"
    And I switch to the <window> window
    And I should <expect1> "URL description"
    Then "Url 1" "link" should <expect2>

    Examples:
      | display | description    | expect1  | expect2                                            | window |
      | 0       | Automatic      | see      | exist in the "#region-main-box" "css_element"      | main   |
      | 1       | Embed          | see      | not exist in the "#region-main-box" "css_element"  | main   |
      | 2       | In frame       | not see  | not exist                                          | main   |
      | 3       | New window     | not see  | not exist                                          | new    |
      | 5       | Open           | not see  | not exist                                          | main   |
      | 6       | In pop-up      | not see  | not exist                                          | new    |
