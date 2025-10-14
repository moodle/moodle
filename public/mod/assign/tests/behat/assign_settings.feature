@mod @mod_assign
Feature: Assignments settings can be changed
  In order to allow managing assignments
  As a teacher
  I need to be able to change various assignment settings.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | maxattempts                         | -1                      |
      | attemptreopenmethod                 | manual                  |
      | hidegrader                          | 1                       |
      | submissiondrafts                    | 0                       |
      | completion                          | 2                       |
      | completionview                      | 1                       |
      | completionusegrade                  | 1                       |
      | gradepass                           | 50                      |
      | completionpassgrade                 | 1                       |
    And the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
      | student2  | Student    | 2         | student2@example.com  |
      | student3  | Student    | 3         | student2@example.com  |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | student1  | C1      | student         |
      | student2  | C1      | student         |
      | student3  | C1      | student         |

  @javascript
  Scenario: Changing Grant Attempts settings on activity page
    # Initially Grant Attempts is set to Manually.
    Given I am on the "Test assignment name" Activity page logged in as student1
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student attempt |
    And I press "Save changes"

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I go to "Student 1" "Test assignment name" activity advanced grading page
    And I set the field "Grade out of 100" to "40"
    And I set the field "Notify student" to "0"
    And I press "Save changes"

    And I am on the "Test assignment name" Activity page logged in as student1
    And "Add a new attempt" "button" should not exist
    And "Add a new attempt based on previous submission" "button" should not exist

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I open the action menu in "Student 1" "table_row"
    And I follow "Allow another attempt"

    And I am on the "Test assignment name" Activity page logged in as student1
    And "Add a new attempt" "button" should exist
    And "Add a new attempt based on previous submission" "button" should exist

    # Set Grant Attempts to Automatically Until Pass.
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And the field "attemptreopenmethod" matches value "manual"
    And I set the following fields to these values:
      | attemptreopenmethod       | untilpass                  |
    And I press "Save and display"

    And I am on the "Test assignment name" Activity page logged in as student2
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student attempt |
    And I press "Save changes"

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I go to "Student 2" "Test assignment name" activity advanced grading page
    And I set the field "Grade out of 100" to "40"
    And I set the field "Notify student" to "0"
    And I press "Save changes"

    And I am on the "Test assignment name" Activity page logged in as student2
    And "Add a new attempt" "button" should exist
    And "Add a new attempt based on previous submission" "button" should exist

    # Set Grant Attempts back to Manually.
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And the field "attemptreopenmethod" matches value "untilpass"
    And I set the following fields to these values:
      | attemptreopenmethod       | manual                  |
    And I press "Save and display"

    And I am on the "Test assignment name" Activity page logged in as student3
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student attempt |
    And I press "Save changes"

    When I am on the "Test assignment name" Activity page logged in as teacher1
    And I change window size to "large"
    And I go to "Student 3" "Test assignment name" activity advanced grading page
    And I change window size to "medium"
    And I set the field "Grade out of 100" to "40"
    And I set the field "Notify student" to "0"
    And I press "Save changes"

    And I am on the "Test assignment name" Activity page logged in as student3
    And "Add a new attempt" "button" should not exist
    And "Add a new attempt based on previous submission" "button" should not exist

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    And I open the action menu in "Student 3" "table_row"
    And I change window size to "medium"
    And I follow "Allow another attempt"

    And I am on the "Test assignment name" Activity page logged in as student3
    Then "Add a new attempt" "button" should exist
    And "Add a new attempt based on previous submission" "button" should exist

  Scenario: Admin cannot add submission if not enrolled as student
    When I am on the "Test assignment name" Activity page logged in as admin
    Then I should not see "Add submission"
    And the following "course enrolments" exist:
      | user     | course | role    |
      | admin    | C1     | teacher |
    And I am on the "Test assignment name" Activity page logged in as admin
    And I should not see "Add submission"
    But the following "course enrolments" exist:
      | user     | course | role    |
      | admin    | C1     | student |
    And I am on the "Test assignment name" Activity page logged in as admin
    And I should see "Add submission"
