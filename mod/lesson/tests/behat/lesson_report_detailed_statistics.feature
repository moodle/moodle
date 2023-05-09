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
    Given I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Numerical question |
      | Page contents | What is 1 + 0.5? |
      | id_answer_editor_0 | 1.5 |
      | id_jumpto_0 | End of lesson |
    And I press "Save page"
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
