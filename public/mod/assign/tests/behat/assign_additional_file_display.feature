@mod @mod_assign
Feature: Additional files for use in assignments can be hidden
  In order to display additional files only during submission
  As a teacher
  I need to check the 'Only show files during submission' checkbox

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript @_file_upload
  Scenario: Additional files are only shown during submission
    Given the following "activities" exist:
      | activity | course | name     | submissionattachments | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled |
      | assign   | C1     | Assign 1 | 1                     | 1                                   | 0                             |
    And I am on the "Assign 1" "assign activity editing" page logged in as teacher1
    And I upload "/mod/assign/tests/fixtures/submissionsample01.txt" file to "Additional files" filemanager
    And I press "Save and return to course"
    When I am on the "Assign 1" "assign activity" page logged in as student1
    # Confirm that Additional files are not displayed on assignment activity when student logs in.
    Then "submissionsample01.txt" "link" should not exist
    And I press "Add submission"
    # Additional files are displayed after student presses "Add submission" button and goes to submission phase.
    And "submissionsample01.txt" "link" should exist
    And following "submissionsample01.txt" should download a file that:
      | Has mimetype  | text/plain                                |
      | Contains text | This is just a submission testing sample. |
