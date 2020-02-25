@qtype @qtype_multichoice
Feature: Clear my answers
  As a student
  In order to reset Multiple choice ansers
  I need to clear my choice

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | student1 | S1        | Student1 | student1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name             | template    | questiontext    |
      | Test questions   | multichoice | Multi-choice-001 | one_of_four | Question One  |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber | preferredbehaviour | canredoquestions |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    | immediatefeedback  | 1                |
    And quiz "Quiz 1" contains the following questions:
      | question         | page |
      | Multi-choice-001 | 1    |

  @javascript
  Scenario: Attempt a quiz and reset my chosen answer.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    And I should see "Question One"
    And I click on "Four" "radio" in the "Question One" "question"
    And I should see "Clear my choice"
    And I click on "Clear my choice" "button" in the "Question One" "question"
    Then I should not see "Clear my choice"
    And I click on "Check" "button" in the "Question One" "question"
    And I should see "Please select an answer" in the "Question One" "question"
