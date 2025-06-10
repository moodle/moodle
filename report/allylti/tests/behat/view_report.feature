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
# Behat feature for Ally report link.
#
# @package    report_allylti
# @copyright  Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@report @report_allylti
Feature: Launch to Ally reports
  In order to view Ally reports
  As an Administrator
  I want to click a link to launch to Ally reports

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | topics |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | adminurl | /report/allylti/tests/fixtures/report.php | tool_ally |
      | key      | ltikey                                    | tool_ally |
      | secret   | secretpassword12345                       | tool_ally |

  @javascript
  Scenario: Administrator can click a link to launch site report when configured
    Given I log in as "admin"
    And I navigate to "Reports > Accessibility" in site administration
    And I switch to "contentframe" iframe
    Then I should see "This represents a report launch"

  Scenario: Teacher does not see a link for the site report
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    Then "Reports > Accessibility" "link" should not exist in current page administration

  @javascript
  Scenario Outline: Teacher can click a link to launch course report, student cannot.
    And the following config values are set as admin:
      | theme | <theme> |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And "a[href*=\"report/allylti/launch.php?reporttype=course\"]" "css_element" should exist
    And I navigate to the course accessibility report
    And I click on "Accessibility report" "link" in the "#settingsnav" "css_element"
    Given I switch to the new window
    And I should see "This represents a report launch"
    And I am on site homepage
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And "a[href*=\"report/allylti/launch.php?reporttype=course\"]" "css_element" should not exist
    And I log out
  Examples:
    |theme  |
    |classic|
