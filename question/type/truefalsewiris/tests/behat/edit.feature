@qtype @qtype_wq @qtype_truefalsewiris
Feature: Test editing a True/False Wiris question
  As a teacher
  In order to be able to update my True/False question
  I need to edit them
  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype          | name                 | template         |
      | Test questions   | truefalsewiris | true-false-wiris-001 | sciencetruefalse |

  Scenario: Edit a True/False question
    When I am on the "true-false-wiris-001" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | |
    And I press "id_submitbutton"
    Then I should see "You must supply a value here."
    When I set the following fields to these values:
      | Question name | Edited true-false-wiris-001 name |
    And I press "id_submitbutton"
    Then I should see "Edited true-false-wiris-001 name"
