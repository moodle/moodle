@mod @mod_assign @_only_local
Feature: In an assignment, students can upload files for assessment
  In order to complete my assignments providing files
  As a student
  I need to upload files from my file system to be assessed

  @javascript
  Scenario: Submit a file and update the submission with another file
    Given the following "courses" exists:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 0 |
      | assignsubmission_file_enabled | 1 |
      | Maximum number of uploaded files | 2 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    And I upload "lib/tests/fixtures/empty.txt" file to "File submissions" filepicker
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I should see "empty.txt"
    And I should see "Not graded"
    And I press "Edit submission"
    And I upload "lib/tests/fixtures/upload_users.csv" file to "File submissions" filepicker
    And ".ffilemanager .fm-maxfiles .fp-btn-add" "css_element" should exists
    And I press "Save changes"
    And I should see "Submitted for grading"
    And I should see "empty.txt"
    And I should see "upload_users.csv"
    And I press "Edit submission"
    And ".ffilemanager .fm-maxfiles .fp-btn-add" "css_element" should exists
