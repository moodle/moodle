@mod @mod_assign @assignsubmission_file
Feature: In an assignment, limit submittable file types
  In order to constrain student submissions for marking
  As a teacher
  I need to limit the submittable file types

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | filetypes | image/png;spreadsheet | assignsubmission_file |

  @javascript
  Scenario: File types validation for an assignment
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | duedate    | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | assignsubmission_file_maxfiles | assignsubmission_file_maxsizebytes |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 1388534400 | 0                                   | 1                             | 1                              | 0                                  |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
    When I set the field "Accepted file types" to "image/png;doesntexist;.anything;unreal/mimetype;nodot"
    And I press "Save and display"
    And I should see "The following file types were not recognised: doesntexist .anything unreal/mimetype nodot"
    And I set the field "Accepted file types" to "image/png;spreadsheet"
    And I press "Save and display"
    And I navigate to "Edit settings" in current page administration
    Then the field "Accepted file types" matches value "image/png;spreadsheet"

  @javascript @_file_upload
  Scenario: Uploading permitted file types for an assignment
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | duedate    | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | assignsubmission_file_maxfiles | assignsubmission_file_maxsizebytes | assignsubmission_file_filetypes |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 1388534400 | 0                                   | 1                             | 3                              | 0                                  | image/png;spreadsheet;.xml;.txt  |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I press "Add submission"
    And I should see "Files of these types may be added to the submission"
    And I should see "Image (PNG) — .png"
    And I should see "Spreadsheet files — .csv .gsheet .ods .ots .xls .xlsx .xlsm"
    And I should see ".txt"
    And I upload "lib/tests/fixtures/gd-logo.png" file to "File submissions" filemanager
    And I upload "lib/tests/fixtures/tabfile.csv" file to "File submissions" filemanager
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filemanager
    And I press "Save changes"
    Then "gd-logo.png" "link" should exist
    And "tabfile.csv" "link" should exist
    And "empty.txt" "link" should exist

  @javascript @_file_upload
  Scenario: No filetypes allows all
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | duedate    | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | assignsubmission_file_maxfiles | assignsubmission_file_maxsizebytes | assignsubmission_file_filetypes |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 1388534400 | 0                                   | 1                             | 2                              | 0                                  |                                 |
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I press "Add submission"
    And I should not see "Files of these types may be added to the submission"
    And I upload "lib/tests/fixtures/gd-logo.png" file to "File submissions" filemanager
    And I upload "lib/tests/fixtures/tabfile.csv" file to "File submissions" filemanager
    And I press "Save changes"
    Then "gd-logo.png" "link" should exist
    And "tabfile.csv" "link" should exist
