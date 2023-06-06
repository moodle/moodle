@mod @mod_lesson
Feature: In a lesson activity, students can see their progress viewing a progress bar.
  In order to create a lesson with conditional paths
  As a teacher
  I need to add pages and questions with links between them

  Scenario: Student navigation with progress bar
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
    And the following "activities" exist:
      | activity   | name             | course | idnumber  |
      | lesson     | Test lesson name | C1     | lesson1   |
    And the following "mod_lesson > pages" exist:
      | lesson           | qtype   | title                 | content              |
      | Test lesson name | content | First page name       | First page contents  |
      | Test lesson name | content | Second page name      | Second page contents |
      | Test lesson name | numeric | Hardest question ever | 1 + 1?               |
    And the following "mod_lesson > answers" exist:
      | page                  | answer        | response         | jumpto          | score |
      | First page name       | Next page     |                  | Next page       | 0     |
      | Second page name      | Previous page |                  | Previous page   | 0     |
      | Second page name      | Next page     |                  | Next page       | 0     |
      | Hardest question ever | 2             | Correct answer   | End of lesson   | 1     |
      | Hardest question ever | 1             | Incorrect answer | First page name | 0     |
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Progress bar | Yes |
    And I press "Save and display"
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "First page contents"
    And I should see "You have completed 0% of the lesson"
    And I press "Next page"
    And I should see "Second page contents"
    And I should see "You have completed 33% of the lesson"
    And I press "Previous page"
    And I should see "First page contents"
    And I should see "You have completed 67% of the lesson"
    And I press "Next page"
    And I should see "Second page contents"
    And I should see "You have completed 67% of the lesson"
    And I press "Next page"
    And I should see "1 + 1?"
    And I should see "You have completed 67% of the lesson"
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I should see "Correct answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "You have completed 100% of the lesson"
