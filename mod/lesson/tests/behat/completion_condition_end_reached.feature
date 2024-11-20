@mod @mod_lesson @javascript
Feature: Set end of lesson reached as a completion condition for a lesson
  In order to ensure students really see all lesson pages
  As a teacher
  I need to set end of lesson reached to mark the lesson activity as completed

  Scenario: Set end reached as a condition
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exist:
      | activity | name        | course | idnumber | completion | completionview | completionendreached |
      | lesson   | Test lesson | C1     | 0001     | 2          | 0              | 1                    |
    And the following "mod_lesson > pages" exist:
      | lesson      | qtype   | title            | content              |
      | Test lesson | content | First page name  | First page contents  |
      | Test lesson | content | Second page name | Second page contents |
    And the following "mod_lesson > answers" exist:
      | page             | answer        | jumpto        |
      | First page name  | Next page     | Next page     |
      | Second page name | Previous page | Previous page |
      | Second page name | Next page     | Next page     |
    When I am on the "Course 1" course page logged in as student1
    Then the "Go through the activity to the end" completion condition of "Test lesson" is displayed as "todo"
    And I follow "Test lesson"
    And I press "Next page"
    And I am on "Course 1" course homepage
    And the "Go through the activity to the end" completion condition of "Test lesson" is displayed as "todo"
    And I am on the "Test lesson" "lesson activity" page
    And I should see "You have seen more than one page of this lesson already."
    And I should see "Do you want to start at the last page you saw?"
    And I click on "No" "link" in the "#page-content" "css_element"
    And I press "Next page"
    And I press "Next page"
    And I am on "Course 1" course homepage
    And the "Go through the activity to the end" completion condition of "Test lesson" is displayed as "done"
    And I am on the "Course 1" course page logged in as teacher1
    And "Student 1" user has completed "Test lesson" activity
