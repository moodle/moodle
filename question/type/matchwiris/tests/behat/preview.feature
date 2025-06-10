@qtype @qtype_wq @qtype_matchwiris
Feature: A student can answer a Match Wiris question type
  In order to answer the question
  As a student
  I need to match the fields

  Background:
    Given the "wiris" filter is "on"
    Given the "mathjaxloader" filter is "disabled"
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Default for C1 |
    And the following "questions" exist:
      | questioncategory | qtype      | name          | template       |
      | Default for C1   | matchwiris | Match Wiris   | foursubq       |
      | Default for C1   | matchwiris | Match Formula | twosubqformula |

  @javascript
  Scenario: A student executes a match wiris question
    When I am on the "Match Wiris" "core_question > preview" page logged in as teacher
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub0')]" to "1"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub1')]" to "2"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub2')]" to "3"
    And I press "Submit and finish"
    Then Generalfeedback should exist

  @javascript
  Scenario: A student executes a match wiris question with formulas and feedback
    When I am on the "Match Formula" "core_question > preview" page logged in as teacher
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub0')]" to "1"
    And I set the field with xpath "//table[@class='answer']//td[@class='control']//select[contains(@id, '1_sub1')]" to "2"
    And I press "Submit and finish"
    Then Generalfeedback should exist
    And Wirisformula should exist
