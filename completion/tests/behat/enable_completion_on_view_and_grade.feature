@core @core_completion
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
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | completion                          | 2                       |
      | completionview                      | 1                       |
      | completionusegrade                  | 1                       |
      | gradepass                           | 50                      |
      | completionpassgrade                 | 1                       |
    And I am on the "Course 1" course page logged in as teacher1
    And "Student First" user has not completed "Test assignment name" activity
    And I am on the "Test assignment name" "assign activity" page logged in as student2
    And I am on the "Test assignment name" "assign activity" page logged in as student1

  Scenario: Confirm completion (incomplete/pass/fail) are set correctly
    Given the following "grade grades" exist:
      | gradeitem            | user     | grade |
      | Test assignment name | student1 | 21.00 |
      | Test assignment name | student2 | 50.00 |
      | Test assignment name | student3 | 30.00 |
    When I am on "Course 1" course homepage
    Then the "View" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "failed"
    And I am on the "Course 1" course page logged in as student2
    And the "View" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "done"
    And I am on the "Course 1" course page logged in as student3
    And the "View" completion condition of "Test assignment name" is displayed as "todo"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "failed"
