@mod @mod_lesson @javascript
Feature: Set time spent as a completion condition for a lesson
  In order to ensure students spend the needed time to study lessons
  As a teacher
  I need to set time spent to mark the lesson activity as completed

  Scenario: Set time spent as a condition
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exist:
      | activity | name        | course | idnumber | completion | completionview | completiontimespentenabled | completiontimespent |
      | lesson   | Test lesson | C1     | 0001     | 2          | 0              | 1                          | 5                   |
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
    Then the "Spend at least 5 secs on this activity" completion condition of "Test lesson" is displayed as "todo"
    And I follow "Test lesson"
    And I press "Next page"
    # Add 1 sec delay so lesson knows a valid attempt has been made in past.
    And I wait "1" seconds
    And I press "Next page"
    And I should see "You completed this lesson in"
    And I should see ", which is less than the required time of 5 secs. You might need to attempt the lesson again."
    And I am on "Course 1" course homepage
    And the "Spend at least 5 secs on this activity" completion condition of "Test lesson" is displayed as "todo"
    And I am on the "Test lesson" "lesson activity" page
    And I press "Next page"
    And I wait "5" seconds
    And I press "Next page"
    And I should not see "You might need to attempt the lesson again."
    And I am on "Course 1" course homepage
    And the "Spend at least 5 secs on this activity" completion condition of "Test lesson" is displayed as "done"
    And I am on the "Course 1" course page logged in as teacher1
    And "Student 1" user has completed "Test lesson" activity
