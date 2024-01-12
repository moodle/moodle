@qtype @qtype_numerical
Feature: Preview a Numerical question
  As a teacher
  In order to check my Numerical questions will work for students
  I need to preview them

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
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

  @javascript @_switch_window
  Scenario: Preview a Numerical question and submit a correct response.
    When I am on the "Numerical-001" "core_question > preview" page logged in as teacher
    And I should see "What is pi to two d.p.?"
    And I expand all fieldsets
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Save preview options and start again"
    And I set the field with xpath "//span[@class='answer']//input[contains(@id, '1_answer')]" to "3.14"
    And I press "Check"
    Then I should see "Very good."
    And I should see "Mark 1#00 out of 1#00"
    And I press "Start again"
    And I set the field with xpath "//span[@class='answer']//input[contains(@id, '1_answer')]" to "3,14"
    And I press "Check"
    And I should see "Please enter your answer without using the thousand separator (,)."
    And I press "Start again"
    And I set the field with xpath "//span[@class='answer']//input[contains(@id, '1_answer')]" to "3#14"
    And I press "Check"
    And I should see "Very good."
    And I should see "Mark 1#00 out of 1#00"

  Scenario: Preview a Numerical question with optional units
    Given I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    # Add the numerical question with optional units
    And I add a "Numerical" question filling the form with:
      | Question name    | Numerical 1                                                                                       |
      | Question text    | What's $500 in Php? <img src="/lib/tests/fixtures/gd-logo.png" alt="img1" width="100" height="75"/> |
      | Default mark     | 1                                                                                                          |
      | General feedback | The correct answer is Php27950 <img src="/lib/tests/fixtures/gd-logo.png" alt="img1" width="100" height="75"/> |
      | id_answer_0      | 27950                                                                                                      |
      | id_tolerance_0   | 0                                                                                                          |
      | id_fraction_0    | 100%                                                                                                       |
      | id_feedback_0    | Correct!                                                                                                   |
      | id_answer_1      | *                                                                                                         |
      | id_tolerance_1   | 0                                                                                                          |
      | id_fraction_1    | 0%                                                                                                         |
      | id_feedback_1    | Wrong!                                                                                                     |
      | id_unitrole      | Units are optional. If a unit is entered, it is used to convert the response to Unit 1 before grading.     |
      | id_unitsleft     | on the left, for example $1.00 or £1.00                                                                    |
      | id_unit_0        | Php                                                                                                        |
      | id_multiplier_0  | 1                                                                                                          |
    # Confirm numerical question can be previewed.
    When I am on the "Numerical 1" "core_question > preview" page logged in as teacher
    Then I should see "Numerical 1"
    And I should see "What's $500 in Php?"
    # Answer question correctly.
    And I set the following fields to these values:
      | Answer | 27950 |
    And I press "Submit and finish"
    # Confirm that corresponding feedback is displayed.
    And I should see "The correct answer is Php27950"
    And I should see "Correct!"
    And "//img[contains(@src, 'gd-logo.png')]" "xpath_element" should exist
    And I press "Start again"
    # Answer question incorrectly.
    And I set the following fields to these values:
      | Answer | 27961 |
    And I press "Submit and finish"
    # Confirm that corresponding feedback is displayed.
    And I should see "The correct answer is Php27950"
    And I should see "Wrong!"
    And "//img[contains(@src, 'gd-logo.png')]" "xpath_element" should exist
