@core @core_completion @javascript
Feature: Students will be marked as completed and pass/fail
  if they have viewed an activity and achieved a grade.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | Frist    | teacher1@example.com |
      | student1 | Student   | First    | student1@example.com |
      | student2 | Student   | Second   | student2@example.com |
      | student3 | Student   | Third    | student3@example.com |
    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |
      | student2 | C1 | student        |
      | student3 | C1 | student        |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | idnumber                            | a1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | completion                          | 2                       |
      | completionview                      | 1                       |
      | completionusegrade                  | 1                       |
      | gradepass                           | 50                      |
      | completionpassgrade                 | 1                       |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And "Student First" user has not completed "Test assignment name" activity
    And I log out
    And I am on the "Test assignment name" "assign activity" page logged in as student2
    And I log out
    And I am on the "Test assignment name" "assign activity" page logged in as student1
    And I log out

  Scenario: Confirm completion (incomplete/pass/fail) are set correctly
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "21" to the user "Student First" for the grade item "Test assignment name"
    And I give the grade "50" to the user "Student Second" for the grade item "Test assignment name"
    And I give the grade "30" to the user "Student Third" for the grade item "Test assignment name"
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And the "View" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "failed"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And the "View" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "done"
    And I log out
    And I log in as "student3"
    And I am on "Course 1" course homepage
    And the "View" completion condition of "Test assignment name" is displayed as "todo"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "failed"

  @javascript
  Scenario: Keep current view completion condition when the teacher does the action 'Unlock completion settings'.
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "21" to the user "Student First" for the grade item "Test assignment name"
    And I give the grade "50" to the user "Student Second" for the grade item "Test assignment name"
    And I press "Save changes"
    And I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I press "Unlock completion settings"
    And I expand all fieldsets
    And I should see "Completion options unlocked"
    And I click on "Save and display" "button"
    And I log out
    When I am on the "Course 1" course page logged in as student1
    Then the "View" completion condition of "Test assignment name" is displayed as "done"
    And I log out
    When I am on the "Course 1" course page logged in as student2
    Then the "View" completion condition of "Test assignment name" is displayed as "done"
