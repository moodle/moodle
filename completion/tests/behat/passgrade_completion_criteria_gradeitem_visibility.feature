@core @core_completion
Feature: Students will be shown relevant completion state based on grade item visibility.
  In order to understand completion states of course modules
  As a student
  I need to see relevant completion information for various combination of activity passgrade settings

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | Frist    | teacher1@example.com |
      | student1 | Student   | First    | student1@example.com |
      | student2 | Student   | Second   | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | completion                          | 2                       |
      | completionpassgrade                 | 1                       |
      | completionusegrade                  | 1                       |
      | gradepass                           | 50                      |
    And I am on the "Course 1" course page logged in as teacher1
    And "Student First" user has not completed "Test assignment name" activity
    And I am on the "Course 1" course page logged in as student1
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "todo"

  Scenario: Passing grade and receive a grade completions for visible grade item (passgrade completion enabled)
    Given the following "grade grades" exist:
      | gradeitem            | user     | grade |
      | Test assignment name | student1 | 21.00 |
      | Test assignment name | student2 | 50.00 |
    And I am on the "Course 1" course page logged in as teacher1
    And "Student First" user has completed "Test assignment name" activity
    And "Student Second" user has completed "Test assignment name" activity
    When I am on the "Course 1" course page logged in as student1
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "failed"
    And I am on the "Course 1" course page logged in as student2
    Then the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "done"

  Scenario: Passing grade and receive a grade completions for hidden grade item (passgrade completion enabled)
    Given I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"
    And I hide the grade item "Test assignment name"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "21" to the user "Student First" for the grade item "Test assignment name"
    And I give the grade "50" to the user "Student Second" for the grade item "Test assignment name"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And "Student First" user has not completed "Test assignment name" activity
    And "Student Second" user has completed "Test assignment name" activity
    And I am on the "Course 1" course page logged in as student1
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "todo"
    And I am on the "Course 1" course page logged in as student2
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "done"

  Scenario: Receive a grade completion for visible grade item (passgrade completion disabled)
    Given I am on the "Test assignment name" "assign activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | completionpassgrade    | 0                 |
    And I press "Save and display"
    And I am on the "Course 1" course page logged in as student1
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "todo"
    And I should not see "Receive a passing grade"
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I turn editing mode on
    And I give the grade "21" to the user "Student First" for the grade item "Test assignment name"
    And I give the grade "50" to the user "Student Second" for the grade item "Test assignment name"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And "Student First" user has completed "Test assignment name" activity
    And "Student Second" user has completed "Test assignment name" activity
    When I am on the "Course 1" course page logged in as student1
    # Once MDL-75582 is fixed "failed" should be changed to "done"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "failed"
    And I should not see "Receive a passing grade"
    And I am on the "Course 1" course page logged in as student2
    Then the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"

  Scenario: Receive a grade completion for hidden grade item (passgrade completion disabled)
    Given I am on the "Test assignment name" "assign activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | completionpassgrade    | 0                 |
    And I press "Save and display"
    And I am on the "Course 1" course page logged in as student1
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "todo"
    And I should not see "Receive a passing grade"
    And I am on the "Course 1" "grades > gradebook setup" page logged in as "teacher1"
    And I hide the grade item "Test assignment name"
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "21" to the user "Student First" for the grade item "Test assignment name"
    And I give the grade "50" to the user "Student Second" for the grade item "Test assignment name"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And "Student First" user has completed "Test assignment name" activity
    And "Student Second" user has completed "Test assignment name" activity
    When I am on the "Course 1" course page logged in as student1
    Then the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And I should not see "Receive a passing grade"
    And I am on the "Course 1" course page logged in as student2
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
