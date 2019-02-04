@qtype @qtype_numerical
Feature: Preview a Numerical question
  As a teacher
  In order to check my Numerical questions will work for students
  I need to preview them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
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
      | questioncategory | qtype     | name          | template |
      | Test questions   | numerical | Numerical-001 | pi       |
      | Test questions   | numerical | Numerical-002 | pi3tries |
    And the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript @_switch_window
  Scenario: Preview a Numerical question and submit a correct response.
    When I click on "Preview" "link" in the "Numerical-001" "table_row"
    And I switch to "questionpreview" window
    Then I should see "What is pi to two d.p.?"
    When I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    And I set the field with xpath "//span[@class='answer']//input[contains(@id, '1_answer')]" to "3.14"
    And I press "Check"
    Then I should see "Very good."
    And I should see "Mark 1#00 out of 1#00"
    When I press "Start again"
    And I set the field with xpath "//span[@class='answer']//input[contains(@id, '1_answer')]" to "3,14"
    And I press "Check"
    Then I should see "Please enter your answer without using the thousand separator (,)."
    When I press "Start again"
    And I set the field with xpath "//span[@class='answer']//input[contains(@id, '1_answer')]" to "3#14"
    And I press "Check"
    Then I should see "Very good."
    And I should see "Mark 1#00 out of 1#00"
    And I switch to the main window
