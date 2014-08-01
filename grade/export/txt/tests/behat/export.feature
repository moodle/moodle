@gradeexport @gradeexport_txt
Feature: I need to export grades as text
  In order to easily review marks
  As a teacher
  I need to have a export grades as text

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | This is a submission |
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Grades"
    And I turn editing mode on
    And I give the grade "80.00" to the user "Student 1" for the grade item "Test assignment name"
    And I press "Update"

  @javascript
  Scenario: Export grades as text
    When I set the field "Grade report" to "Plain text file"
    And I expand all fieldsets
    And I click on "Course total" "checkbox"
    And I set the field "Grade export decimal points" to "1"
    And I press "Download"
    Then I should see "Student,1"
    And I should see "80.0"
    And I should not see "Course total"
    And I should not see "80.00"
