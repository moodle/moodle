@core @core_question
Feature: A bank view with questions can be managed
  In order to manage a question bank from the course
  As a teacher
  I need to be able to view and manage questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | weeks |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name    | course | idnumber |
      | qbank    | Qbank 1 | C1     | qbank1   |
    And the following "question categories" exist:
      | contextlevel    | reference | name           |
      | Activity module | qbank1    | Test questions |

  @javascript
  Scenario: Viewing question bank should not load individual questions
    When the following "questions" exist:
    | questioncategory | qtype       | name                    | questiontext                  | idnumber |
    | Test questions   | essay       | Essay test question     | Write about whatever you want | qid      |
    | Test questions   | numerical   | Numerical test question | Write about whatever you want | qid      |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I navigate to "Question banks" in current page administration
    And I click on "Qbank 1" "link"
    And I should see "Essay test question"
    And I should see "Numerical test question"
    And I choose "Delete" action for "Essay test question" in the question bank
    And I press "Delete"
    And I should not see "Essay test question"
    And I choose "Delete" action for "Numerical test question" in the question bank
    And I press "Delete"

  @javascript
  Scenario: Unknown qtype does not break the view
    When the following "questions" exist:
    | questioncategory | qtype         | name                    | questiontext                  |
    | Test questions   | missingtype   | Unknown type question   | Write about whatever you want |
    | Test questions   | truefalse     | Truefalse type question | Write about whatever you want |
    | Test questions   | essay         | Essay type question     | Write about whatever you want |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I navigate to "Question banks" in current page administration
    And I click on "Qbank 1" "link"
    And I should see "Unknown type question"
    And I should see "Truefalse type question"
    And I should see "Essay type question"
