@qtype @qtype_wq @qtype_essaywiris
Feature: A student can answer a Wiris Essay question type
  In order to answer the question
  As a student
  I need to fill in the essay field

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
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype      | name        | template     |
      | Test questions   | essaywiris | Essay wiris | scienceessay |

  @javascript
  Scenario: A student executes an essay
    When I am on the "Essay wiris" "core_question > preview" page logged in as teacher
    Then Wirisformula should has width "33" with error of "2"
    Then Wirisformula should has height "20" with error of "14"
    And I type "My answer" in "//div[@contenteditable]"
    And I press "Submit and finish"
    Then I should see "Complete"
