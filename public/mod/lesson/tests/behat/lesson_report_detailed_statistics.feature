@mod @mod_lesson
Feature: In a lesson activity, teachers can view detailed statistics report
  To review detailed statistics in a lesson
  As a Teacher
  I need to ve able to navigate to detailed statistics report page.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity   | name             | course | idnumber    | retake |
      | lesson     | Test lesson name | C1     | lesson1     | 1      |
    And I am on the "Test lesson name" "lesson activity" page logged in as teacher1

  Scenario: View detailed statistics in a lesson when empty string is given as answer
    Given the following "mod_lesson > page" exist:
      | lesson           | qtype   | title              | content          |
      | Test lesson name | numeric | Numerical question | What is 1 + 0.5? |
    And the following "mod_lesson > answer" exist:
      | page               | answer | jumpto          | score |
      | Numerical question | 1.5    | End of lesson   | 1     |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I press "Submit"
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I set the field "Your answer" to "1.5"
    And I press "Submit"
    And I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    And I select "Detailed statistics" from the "jump" singleselect
    Then I should see "50% entered this."
