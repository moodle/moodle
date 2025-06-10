@qtype @qtype_wq @qtype_shortanswerwiris
Feature: Test editing a Short answer wiris question
  As a teacher
  In order to be able to update my Short answer wiris question
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
      | questioncategory | qtype            | name                  | template           |
      | Test questions   | shortanswerwiris | shortanswer-wiris-001 | scienceshortanswer |

  @javascript
  Scenario: Edit a Short answer question with algorithm edition
    When I am on the "shortanswer-wiris-001" "core_question > edit" page logged in as teacher
    And I wait "1" seconds
    And I open Wiris Quizzes Studio Instance "2"
    And I wait "3" seconds
    And I type "x+#b"
    And I save Wiris Quizzes Studio
    And I set the following fields to these values:
      | Question name        | Edited shortanswer-wiris-001       |
      | Question text        | Compute x plus #a or x plus #b     |
      | id_fraction_1        | 70%                                |
      | id_feedback_1        | Well at least you did something.   |
    And I press "id_submitbutton"
    Then I should see "Edited shortanswer-wiris-001"
