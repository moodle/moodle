@mod @mod_scorm
Feature: Add scorm activity
  In order to let students access a scorm package
  As a teacher
  I need to add scorm activity to a course

  @javascript
  Scenario: Add a scorm activity to a course
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "SCORM package" to section "1"
    And I upload "mod/scorm/tests/packages/singlescobasic.zip" file to "Package file" filepicker
    And I fill the moodle form with:
      | Name | Awesome SCORM package |
      | Description | Description |
    And I click on "Save and display" "button"
    Then I should see "Awesome SCORM package"
    And I should see "Normal"
    And I should see "Preview"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Awesome SCORM package"
    And I should see "Normal"
    And I press "Enter"
    And I should see "Golf Explained"
