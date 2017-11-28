@core @core_question
Feature: A teacher can preview questions in the question bank
  In order to ensure the questions are properly created
  As a teacher
  I need to preview the questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | weeks  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype     | name                          |
      | Test questions   | numerical | Test question to be previewed |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Questions" node in "Course administration > Question bank"
    When I click on "Preview" "link" in the "Test question to be previewed" "table_row"
    And I switch to "questionpreview" window

  @javascript @_switch_window
  Scenario: Question preview shows the question and other information
    Then the state of "What is pi to two d.p.?" question is shown as "Not yet answered"
    And I should see "Marked out of 1.00"
    And I should see "Technical information"
    And I should see "Attempt options"
    And I should see "Display options"

  @javascript @_switch_window
  Scenario: Preview lets the teacher see what happens when an answer is saved
    When I set the field "Answer:" to "1"
    And I press "Save"
    Then the state of "What is pi to two d.p.?" question is shown as "Answer saved"

  @javascript @_switch_window
  Scenario: Preview lets the teacher see what happens when an answer is submitted
    When I set the field "Answer:" to "3.14"
    And I press "Submit and finish"
    Then the state of "What is pi to two d.p.?" question is shown as "Correct"

  @javascript @_switch_window
  Scenario: Preview lets the teacher see what happens with different review options
    Given I set the field "Answer:" to "3.14"
    And I press "Submit and finish"
    When I set the field "Whether correct" to "Not shown"
    And I set the field "Decimal places in grades" to "5"
    And I press "Update display options"
    Then the state of "What is pi to two d.p.?" question is shown as "Complete"
    And I should see "1.00000"

  @javascript @_switch_window
  Scenario: Preview lets the teacher see what happens with different behaviours
    When I set the field "How questions behave" to "Immediate feedback"
    And I set the field "Marked out of" to "3"
    And I press "Start again with these options"
    And I set the field "Answer:" to "3.1"
    And I press "Check"
    Then the state of "What is pi to two d.p.?" question is shown as "Incorrect"
    And I should see "Mark 0.00 out of 3.00"
    And I should see "Not accurate enough."

  @javascript @_switch_window
  Scenario: Preview lets the teacher "Start again" while previewing
    Given I set the field "Answer:" to "1"
    And I press "Submit and finish"
    When I press "Start again"
    Then the state of "What is pi to two d.p.?" question is shown as "Not yet answered"

  @javascript @_switch_window
  Scenario: Preview lets the teacher "Fill in correct response" while previewing
    When I press "Fill in correct responses"
    Then the field "Answer:" matches value "3.14"
