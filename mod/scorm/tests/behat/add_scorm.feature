@mod @mod_scorm @_file_upload @_switch_iframe
Feature: Add scorm activity
  In order to let students access a scorm package
  As a teacher
  I need to add scorm activity to a course

  @javascript
  Scenario: Add a scorm activity to a course
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "SCORM package" to section "1"
    And I set the following fields to these values:
      | Name | Awesome SCORM package |
      | Description | Description |
    And I upload "mod/scorm/tests/packages/singlesco_scorm12.zip" file to "Package file" filemanager
    And I click on "Save and display" "button"
    Then I should see "Awesome SCORM package"
    And I should see "Enter"
    And I should see "Preview"
    And I log out
    And I am on the "Awesome SCORM package" "scorm activity" page logged in as student1
    And I should see "Enter"
    And I press "Enter"
    And I switch to "scorm_object" iframe
    And I should see "Not implemented yet"
    And I switch to the main frame
    And I am on "Course 1" course homepage
