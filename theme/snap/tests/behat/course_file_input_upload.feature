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
# Tests for html5 file upload direct to course.
#
# @package    theme_snap
# @copyright  Copyright (c) 2016 Open LMS (https://www.openlms.net)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@theme @theme_snap @theme_snap_course
Feature: When the moodle theme is set to Snap, teachers can upload files as resources directly to the current
  course section from a simple file input element in either read or edit mode.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | format | maxbytes | enablecompletion | initsections |
      | Course 1 | C1        | 0        | topics | 500000   | 1                |      1       |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: In read mode, teacher uploads file.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And "#snap-drop-file-1" "css_element" should exist
    And I upload file "test_text_file.txt" to section 1
    And I upload file "test_mp3_file.mp3" to section 1
    Then ".snap-resource[data-type='txt']" "css_element" should exist
    And ".snap-resource[data-type='mp3']" "css_element" should exist
    # Make sure image uploads do not suffer from annoying prompt for label handler.
    And I upload file "testgif.gif" to section 1
    Then I should not see "Add image to course page"
    And I should not see "Create file resource"
    And I should see "testgif" in the "#section-1 .snap-native-image .activityinstance .instancename" "css_element"

  @javascript
  Scenario: Student cannot upload file.
    Given I log in as "student1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#snap-drop-file" "css_element" should not exist

  @javascript
  Scenario: A teacher with the capability should be able to upload a file with any size.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And "#snap-drop-file-1" "css_element" should exist
    And I upload file "400KB_file.txt" to section 1
    And I upload file "600KB_file.mp3" to section 1
    Then ".snap-resource[data-type='txt']" "css_element" should exist
    And ".snap-resource[data-type='mp3']" "css_element" should not exist
    And I should see "The file '600KB_file.mp3' is too large and cannot be uploaded"
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I click on "#admin-menu-trigger" "css_element"
    And I navigate to "Users > Permissions" in current page administration
    And I override the system permissions of "Teacher" role with:
      | capability                         | permission |
      | moodle/course:ignorefilesizelimits | Allow      |
    And I log out
    And I log in as "teacher1"
    And I am on the course main page for "C1"
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And "#snap-drop-file-1" "css_element" should exist
    And I upload file "600KB_file.mp3" to section 1
    And ".snap-resource[data-type='mp3']" "css_element" should exist

  @javascript
  Scenario Outline: A user should see a header when viewing file depending on display options.
    Given I log in as "admin"
    And the following config values are set as admin:
      | displayoptions | <display>  | resource |
    And I am on "Course 1" course homepage
    And I add a resource activity to course "Course 1" section "1"
    And I expand all fieldsets
    And I set the field "Students must manually mark the activity as done" to "1"
    And I click on "id_completionexpected_enabled" "checkbox"
    And I set the following fields to these values:
      | Name                          | Myfile                  |
      | id_completionexpected_enabled | 1                       |
      | id_completionexpected_day     | ##tomorrow##%d##        |
      | id_completionexpected_month   | ##tomorrow##%B##        |
      | id_completionexpected_year    | ##tomorrow##%Y##        |
    And I upload "theme/snap/tests/fixtures/400KB_file.txt" file to "Select files" filemanager
    And I press "Save and return to course"
    And I log out
    Given I log in as "student1"
    And I wait until the page is ready
    And I click on "#snap_feeds_side_menu_trigger" "css_element"
    And I follow "Myfile should be completed"
    Then I <visible> "Mark as done"
    And I log out

    Examples:
      | display | visible        |
      | 0       | should not see |
      | 1       | should see     |
      | 2       | should not see |
      | 3       | should see     |
      | 5       | should not see |
      | 6       | should see     |

  @javascript @_switch_window @theme_snap_file_upload
  Scenario Outline: A user should see the display format when viewing file depending on display options.
    Given I log in as "admin"
    And the following config values are set as admin:
      | displayoptions     | <display>| resource   |
      | displaydescription | 0        | theme_snap |
    And I am on "Course 1" course homepage
    And I add a resource activity to course "Course 1" section "1"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Name                      | Myfile  |
    And I upload "theme/snap/tests/fixtures/400KB_file.txt" file to "Select files" filemanager
    And I press "Save and return to course"
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on ".mod-link" "css_element"
    And I switch to the <window> window
    Then "Myfile" "text" <exist>
    And I log out
    Examples:
      | display | exist            | window         |
      | 1       | should exist     | main           |
      | 2       | should exist     | main           |
      | 3       | should exist     | main           |


  @javascript @_switch_window @theme_snap_file_upload
  Scenario Outline: A file with display pop-up should open a new window with the file
    Given I am logged in as "admin"
    And the following config values are set as admin:
      | displayoptions     | <display> | resource   |
      | displaydescription | 0         | theme_snap |
    And I am on "Course 1" course homepage
    And I add a resource activity to course "Course 1" section "1"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Name | Myfile |
    And I upload "theme/snap/tests/fixtures/test_text_file.txt" file to "Select files" filemanager
    And I press "Save and return to course"
    And I log out
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Section 1"
    And I click on ".mod-link" "css_element"
    And I switch to a second window
    Then "This is just some test text" "text" <exist>
    And I log out
    Examples:
      | display | exist            |
      | 6       | should exist |

  @javascript
  Scenario: User can upload file with edit mode enabled.
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    And I switch edit mode in Snap
    And I follow "Section 1"
    Then "#section-1" "css_element" should exist
    And "#snap-drop-file-1" "css_element" should exist
    And I upload file "test_text_file.txt" to section 1
    And I upload file "test_mp3_file.mp3" to section 1
    Then ".snap-resource[data-type='txt']" "css_element" should exist
    And ".snap-resource[data-type='mp3']" "css_element" should exist
    And I upload file "testgif.gif" to section 1
    Then I should not see "Add image to course page"
    And I should not see "Create file resource"
    And I should see "testgif" in the "#section-1 .snap-native-image .activityinstance .instancename" "css_element"