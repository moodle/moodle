@mod @mod_lesson
Feature: In a lesson activity, teacher can delete question answers and
branch table contents
  In order to modify an existing lesson
  As a teacher
  I need to question answers and branch table contents in the lesson

  Background:
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
      | activity   | name             | course | idnumber    |
      | lesson     | Test lesson name | C1     | lesson1     |
    And the following "mod_lesson > pages" exist:
      | lesson           | qtype   | title                 | content             |
      | Test lesson name | content | First page name       | First page contents |
      | Test lesson name | numeric | Hardest question ever | 1 + 1?              |
    And the following "mod_lesson > answers" exist:
      | page                  | answer        | response         | jumpto          | score |
      | First page name       | Next page     |                  | Next page       | 0     |
      | First page name       | Previous page |                  | Previous page   | 0     |
      | Hardest question ever | 2             | Correct answer   | End of lesson   | 1     |
      | Hardest question ever | 1             | Incorrect answer | First page name | 0     |
    And I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I press "Edit lesson"
    And I select edit type "Expanded"

  Scenario: Edit lesson content page
    Given I click on "//th[normalize-space(.)='First page name']/descendant::a[3]" "xpath_element"
    When I set the following fields to these values:
      | id_answer_editor_1 | |
    And I press "Save page"
    And I should not see "Previous page"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "First page contents"
    And I should not see "Previous page"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I should see "Correct answer"
    And I should not see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 1)."

  Scenario: Edit lesson question page
    Given I click on "//th[normalize-space(.)='Hardest question ever']/descendant::a[3]" "xpath_element"
    When I set the following fields to these values:
      | id_answer_editor_1 | |
    And I press "Save page"
    And I should not see "Incorrect answer"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    Then I should see "First page contents"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should not see "Incorrect answer"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 0 (out of 1)."
