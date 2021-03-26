@mod @mod_quiz
Feature: Set a quiz to be marked complete when the student passes
  In order to ensure a student has learned the material before being marked complete
  As a teacher
  I need to set a quiz to complete when the student recieves a passing grade

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
    And the following config values are set as admin:
      | grade_item_advanced | hiddenuntil |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | questiontext              |
      | Test questions   | truefalse | First question | Answer the first question |
    And the following "activities" exist:
      | activity | name           | course | idnumber | attempts | gradepass | completion | completionusegrade | completionpass |
      | quiz     | Test quiz name | C1     | quiz1    | 4        | 5.00      | 2          | 1                  | 1              |
    And quiz "Test quiz name" contains the following questions:
      | question       | page |
      | First question | 1    |

  Scenario: student1 passes on the first try
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And the "Receive a grade" completion condition of "Test quiz name" is displayed as "todo"
    And the "Receive a pass grade" completion condition of "Test quiz name" is displayed as "todo"
    And user "student1" has attempted "Test quiz name" with responses:
      | slot | response |
      |   1  | True     |
    And I am on "Course 1" course homepage
    Then the "Receive a grade" completion condition of "Test quiz name" is displayed as "done"
    And the "Receive a pass grade" completion condition of "Test quiz name" is displayed as "done"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Activity completion" in current page administration
    And "Completed" "icon" should exist in the "Student 1" "table_row"
