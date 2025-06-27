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
# Tests for forum attachments processed by Ally filter.
#
# @package    filter_ally
# @author     Guy Thomas
# @copyright  Copyright (c) 2017 Open LMS / 2023 Anthology Inc. and its affiliates
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@filter @filter_ally @_file_upload @suite_ally
Feature: When the ally filter is enabled ally place holders are inserted when appropriate into forum attachments.

  Background:
    Given the ally filter is enabled

  @javascript
  Scenario Outline: Forum attachments are processed appropriately.
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
      | teacher1 | C1     | editingteacher        |
    And the following config values are set as admin:
      | config              | value            |
      | slasharguments      | <slasharguments> |
    And the following "activities" exist:
      | activity   | name             | intro                  | type    | course   | section |
      | <forumtype>| Test forum name  | Test forum description | general | C1       | 1       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
#    And I add a "<forumtypestr>" to section "1" and I fill the form with:
#      | Forum name | Test forum name |
#      | Forum type | Standard forum for general use |
#      | Description | Test forum description |
    And I add a new discussion to "Test forum name" <forumtypestr> with:
      | Subject | Teacher discussion |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/empty.txt |
    And I reply "Teacher discussion" post from "Test forum name" <forumtypestr> with:
      | Subject | Teacher reply (non image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    And I reply "Teacher discussion" post from "Test forum name" <forumtypestr> with:
      | Subject | Teacher reply (image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/gd-logo.png |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I reply "Teacher discussion" post from "Test forum name" <forumtypestr> with:
      | Subject | Student reply (non image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    And I reply "Teacher discussion" post from "Test forum name" <forumtypestr> with:
      | Subject | Student reply (image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Test forum name" "link" in the ".activityname" "css_element"
    And I follow "Teacher discussion"
    And I should see the feedback place holder for the post entitled "Teacher discussion" by "Teacher 1"
    And I should see the download place holder for the post entitled "Teacher discussion" by "Teacher 1"
    And I should see the feedback place holder for the post entitled "Teacher reply (non image file)" by "Teacher 1"
    And I should see the download place holder for the post entitled "Teacher reply (non image file)" by "Teacher 1"
    And I should not see the download place holder for the post entitled "Teacher reply (image file)" by "Teacher 1"
    And I should see the feedback place holder for the post entitled "Teacher reply (image file)" by "Teacher 1"
    # Student attachments should not be processed.
    And I should not see the feedback place holder for the post entitled "Student reply (non image file)" by "Student 1"
    And I should not see the download place holder for the post entitled "Student reply (non image file)" by "Student 1"
    And I should not see the feedback place holder for the post entitled "Student reply (image file)" by "Student 1"
    And I should not see the download place holder for the post entitled "Student reply (image file)" by "Student 1"
    And I log out
    # Check placeholders for students.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I follow "Teacher discussion"
    And I should not see the feedback place holder for the post entitled "Teacher discussion" by "Teacher 1"
    And I should see the download place holder for the post entitled "Teacher discussion" by "Teacher 1"
    And I should not see the feedback place holder for the post entitled "Teacher reply (non image file)" by "Teacher 1"
    And I should see the download place holder for the post entitled "Teacher reply (non image file)" by "Teacher 1"
    And I should not see the download place holder for the post entitled "Teacher reply (image file)" by "Teacher 1"
    And I should not see the feedback place holder for the post entitled "Teacher reply (image file)" by "Teacher 1"
    # Student attachments should not be processed.
    And I should not see the feedback place holder for the post entitled "Student reply (non image file)" by "Student 1"
    And I should not see the download place holder for the post entitled "Student reply (non image file)" by "Student 1"
    And I should not see the feedback place holder for the post entitled "Student reply (image file)" by "Student 1"
    And I should not see the download place holder for the post entitled "Student reply (image file)" by "Student 1"
  Examples:
  | forumtypestr      | forumtype         | slasharguments |
  | Open Forum        | hsuforum          | 1              |
  | forum             | forum             | 1              |
  | Open Forum        | hsuforum          | 0              |
  | forum             | forum             | 0              |

  @javascript
  Scenario Outline: Social format attachments are processed appropriately.
    Given the following "courses" exist:
      | fullname | shortname | category | format |
      | Course 1 | C1        | 0        | social |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config              | value            |
      | slasharguments      | <slasharguments> |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I add a new discussion to "Social forum" forum with:
      | Subject | Teacher discussion |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/empty.txt |
    And I reply "Teacher discussion" post from "Social forum" forum with:
      | Subject | Teacher reply (non image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    And I reply "Teacher discussion" post from "Social forum" forum with:
      | Subject | Teacher reply (image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/gd-logo.png |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I reply "Teacher discussion" post from "Social forum" forum with:
      | Subject | Student reply (non image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    And I reply "Teacher discussion" post from "Social forum" forum with:
      | Subject | Student reply (image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Discuss this topic"
    And I should see the feedback place holder for the post entitled "Teacher discussion" by "Teacher 1"
    And I should see the download place holder for the post entitled "Teacher discussion" by "Teacher 1"
    And I should see the feedback place holder for the post entitled "Teacher reply (non image file)" by "Teacher 1"
    And I should see the download place holder for the post entitled "Teacher reply (non image file)" by "Teacher 1"
    And I should not see the download place holder for the post entitled "Teacher reply (image file)" by "Teacher 1"
    And I should see the feedback place holder for the post entitled "Teacher reply (image file)" by "Teacher 1"
    # Student attachments should not be processed.
    And I should not see the feedback place holder for the post entitled "Student reply (non image file)" by "Student 1"
    And I should not see the download place holder for the post entitled "Student reply (non image file)" by "Student 1"
    And I should not see the feedback place holder for the post entitled "Student reply (image file)" by "Student 1"
    And I should not see the download place holder for the post entitled "Student reply (image file)" by "Student 1"
    And I log out
    # Check placeholders for students.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Discuss this topic"
    And I should not see the feedback place holder for the post entitled "Teacher discussion" by "Teacher 1"
    And I should see the download place holder for the post entitled "Teacher discussion" by "Teacher 1"
    And I should not see the feedback place holder for the post entitled "Teacher reply (non image file)" by "Teacher 1"
    And I should see the download place holder for the post entitled "Teacher reply (non image file)" by "Teacher 1"
    And I should not see the download place holder for the post entitled "Teacher reply (image file)" by "Teacher 1"
    And I should not see the feedback place holder for the post entitled "Teacher reply (image file)" by "Teacher 1"
    # Student attachments should not be processed.
    And I should not see the feedback place holder for the post entitled "Student reply (non image file)" by "Student 1"
    And I should not see the download place holder for the post entitled "Student reply (non image file)" by "Student 1"
    And I should not see the feedback place holder for the post entitled "Student reply (image file)" by "Student 1"
    And I should not see the download place holder for the post entitled "Student reply (image file)" by "Student 1"
  Examples:
  | slasharguments |
  | 1              |
  | 0              |

  @javascript
  Scenario Outline: Forum posts annotations are added.
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
      | teacher1 | C1     | editingteacher        |
    And the following config values are set as admin:
      | config              | value            |
      | slasharguments      | <slasharguments> |
    And the following "activities" exist:
      | activity   | name             | intro                  | type    | course   | section |
      | <forumtype>| Test forum name  | Test forum description | general | C1       | 1       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
#    And I add a "<forumtypestr>" to section "1" and I fill the form with:
#      | Forum name | Test forum name |
#      | Forum type | Standard forum for general use |
#      | Description | Test forum description |
    And I add a new discussion to "Test forum name" <forumtypestr> with:
      | Subject | Teacher discussion |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/empty.txt |
    And I reply "Teacher discussion" post from "Test forum name" <forumtypestr> with:
      | Subject | Teacher reply (non image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    And I reply "Teacher discussion" post from "Test forum name" <forumtypestr> with:
      | Subject | Teacher reply (image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/gd-logo.png |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I reply "Teacher discussion" post from "Test forum name" <forumtypestr> with:
      | Subject | Student reply (non image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    And I reply "Teacher discussion" post from "Test forum name" <forumtypestr> with:
      | Subject | Student reply (image file) |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Test forum name" "link" in the ".activityname" "css_element"
    And I follow "Teacher discussion"
    Then Forum should be annotated

    Examples:
      | forumtypestr      | forumtype         | slasharguments |
      | forum             | forum             | 1              |
      | forum             | forum             | 0              |
