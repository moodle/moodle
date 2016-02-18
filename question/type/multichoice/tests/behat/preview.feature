@qtype @qtype_multichoice
Feature: Preview a Multiple choice question
  As a teacher
  In order to check my Multiple choice questions will work for students
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
      | questioncategory | qtype       | name             | template    |
      | Test questions   | multichoice | Multi-choice-001 | two_of_four |
      | Test questions   | multichoice | Multi-choice-002 | one_of_four |
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"

  @javascript @_switch_window
  Scenario: Preview a Multiple choice question and submit a partially correct response.
    When I click on "Preview" "link" in the "Multi-choice-001" "table_row"
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on "One" "checkbox"
    And I click on "Two" "checkbox"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Two is even"
    And I should see "Mark 0.50 out of 1.00"
    And I should see "Parts, but only parts, of your response are correct."
    And I switch to the main window

  @javascript @_switch_window
  Scenario: Preview a Multiple choice question and submit a correct response.
    When I click on "Preview" "link" in the "Multi-choice-001" "table_row"
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on "One" "checkbox"
    And I click on "Three" "checkbox"
    And I press "Check"
    Then I should see "One is odd"
    And I should see "Three is odd"
    And I should see "Mark 1.00 out of 1.00"
    And I should see "Well done!"
    And I should see "The odd numbers are One and Three."
    And I should see "The correct answer is: One, Three"
    And I switch to the main window

  @javascript @_switch_window
  Scenario: Preview a Multiple choice question and submit a correct response.
    When I click on "Preview" "link" in the "Multi-choice-002" "table_row"
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I click on "One" "radio"
    And I press "Check"
    Then I should see "The oddest number is One."
    And I should see "Mark 1.00 out of 1.00"
    And I should see "Well done!"
    And I should see "The correct answer is: One"
    And I switch to the main window
