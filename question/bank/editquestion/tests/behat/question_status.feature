@qbank @qbank_editquestion
Feature: Use the qbank base view to test the status change using
  the pop up

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course         | C1     | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |

  @javascript
  Scenario: Question status modal should change the status of the question
    Given I log in as "admin"
    And I am on the "Test quiz" "quiz activity" page
    And I navigate to "Question bank > Questions" in current page administration
    And I set the field "Select a category" to "Test questions"
    And I should see "Test questions"
    And I should see "Ready" on the status column
    When I click "Ready" on the status column
    Then I should see "Change question status"
    And I should see "Question status"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    And I should see "Ready" on the status column
