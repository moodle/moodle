@qtype @qtype_match
Feature: Test editing a Matching question
  As a teacher
  In order to be able to update my Matching question
  I need to edit them

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype | name                 | template |
      | Test questions   | match | Matching for editing | foursubq |

  @javascript @_switch_window
  Scenario: Edit a Matching question
    When I am on the "Matching for editing" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | |
    And I press "id_submitbutton"
    And I should see "You must supply a value here."
    And I set the following fields to these values:
      | Question name | Edited Matching name |
    And I press "id_submitbutton"
    And I should see "Edited Matching name"
    And I choose "Edit question" action for "Edited Matching name" in the question bank
    And I set the following fields to these values:
      | Shuffle    | 0   |
      | Question 2 | dog |
      | Question 4 | fly |
    And I press "id_submitbutton"
    Then I should see "Edited Matching name"
    And I choose "Preview" action for "Edited Matching name" in the question bank
    And I should see "frog"
    And I should see "dog"
    And I should see "newt"
    And I should see "fly"
