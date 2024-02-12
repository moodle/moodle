@mod @mod_quiz @core_completion @javascript
Feature: Set a quiz to be marked complete when the student completes a minimum amount of attempts
  In order to ensure a student has completed the quiz before being marked complete
  As a teacher
  I need to set a quiz to complete when the student completes a certain amount of attempts

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And the following "activities" exist:
      | activity | name           | course | idnumber | completion | completionminattemptsenabled | completionminattempts |
      | quiz     | Test quiz name | C1     | quiz1    | 2          | 1                            | 2                     |
    And quiz "Test quiz name" contains the following questions:
      | question       | page |
      | First question | 1    |
    And user "student1" has attempted "Test quiz name" with responses:
      | slot | response |
      |   1  | False    |

  Scenario: student1 uses up both attempts without passing
    When I am on the "Course 1" course page logged in as teacher1
    And "Completed: Test quiz name" "icon" should not exist in the "Test quiz name" "list_item"
    And I log out
    And I am on the "Course 1" course page logged in as student1
    And the "Make attempts: 2" completion condition of "Test quiz name" is displayed as "todo"
    And I click on "Test quiz name" "link" in the "region-main" "region"
    And I press "Re-attempt quiz"
    And I set the field "False" to "1"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Submit all your answers and finish?" "dialogue"
    And I am on "Course 1" course homepage
    Then the "Make attempts: 2" completion condition of "Test quiz name" is displayed as "done"
    And I click on "Test quiz name" "link" in the "region-main" "region"
    And the "Make attempts: 2" completion condition of "Test quiz name" is displayed as "done"
    And I log out
    And I am on the "Course 1" course page logged in as teacher1
    And I click on "Test quiz name" "link" in the "region-main" "region"
    And "Test quiz name" should have the "Make attempts: 2" completion condition
    And I am on "Course 1" course homepage
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Completed" "icon" should exist in the "Student 1" "table_row"
