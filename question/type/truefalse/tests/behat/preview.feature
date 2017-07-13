@qtype @qtype_truefalse
Feature: Preview a Trtue/False question
  As a teacher
  In order to check my True/False questions will work for students
  I need to preview them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name           | template |
      | Test questions   | truefalse | true-false-001 | true     |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" node in "Course administration"

  @javascript @_switch_window
  Scenario: Preview a True/False question and submit a correct response.
    When I click on "Preview" "link" in the "true-false-001" "table_row"
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on "True" "radio"
    And I press "Check"
    And I should see "This is the right answer."
    And I should see "The correct answer is 'True'."
    And I switch to the main window

  @javascript @_switch_window
  Scenario: Preview a True/False question and submit an incorrect response.
    When I click on "Preview" "link" in the "true-false-001" "table_row"
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on "False" "radio"
    And I press "Check"
    And I should see "This is the wrong answer."
    And I should see "You should have selected true."
    And I should see "The correct answer is 'True'."
    And I switch to the main window
