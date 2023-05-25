@mod @mod_lti @core_completion
Feature: Pass grade activity completion information in the LTI activity

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | student2 | Vinnie    | Student2 | student2@example.com |
      | student3 | Vinnie    | Student3 | student3@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 0        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name           | course | gradepass | completion | completionview | completionusegrade | completionpassgrade |
      | lti      | Music history  | C1     | 50        | 2          | 1              | 1                  | 1                   |

  Scenario: View automatic completion items as a teacher
    Given I am on the "Music history" "lti activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "Receive a passing grade" completion condition

  @javascript
  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "lti activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "90.00" to the user "Vinnie Student1" for the grade item "Music history"
    And I give the grade "20.00" to the user "Vinnie Student2" for the grade item "Music history"
    And I press "Save changes"
    When I am on the "Music history" "lti activity" page logged in as student1
    Then the "Receive a grade" completion condition of "Music history" is displayed as "done"
    Then the "Receive a passing grade" completion condition of "Music history" is displayed as "done"
    And the "View" completion condition of "Music history" is displayed as "done"
    When I am on the "Music history" "lti activity" page logged in as student2
    Then the "Receive a grade" completion condition of "Music history" is displayed as "done"
    Then the "Receive a passing grade" completion condition of "Music history" is displayed as "failed"
    And the "View" completion condition of "Music history" is displayed as "done"
    When I am on the "Music history" "lti activity" page logged in as student3
    Then the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    Then the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And the "View" completion condition of "Music history" is displayed as "done"
    And I am on the "Course 1" course page logged in as teacher1
    And "Vinnie Student1" user has completed "Music history" activity
    And "Vinnie Student2" user has completed "Music history" activity
    And "Vinnie Student3" user has not completed "Music history" activity
