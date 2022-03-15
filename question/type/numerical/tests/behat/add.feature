@qtype @qtype_numerical
Feature: Test creating a Numerical question
  As a teacher
  In order to test my students
  I need to be able to create a Numerical question

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
    And the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | #     |

  Scenario: Create a Numerical question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Numerical" question filling the form with:
      | Question name                      | Numerical-001                          |
      | Question text                      | What is the average of 4, 5, 6 and 10? |
      | Default mark                       | 1                                      |
      | General feedback                   | The average is 6.25                    |
      | id_answer_0                        | 6.25                                   |
      | id_tolerance_0                     | 0.05                                   |
      | id_fraction_0                      | 100%                                   |
      | id_answer_1                        | 2#25                                   |
      | id_tolerance_1                     | 0#05                                   |
      | id_fraction_1                      | 0%                                     |
      | id_answer_2                        | 5,1                                    |
      | id_tolerance_2                     | 0                                      |
      | id_fraction_2                      | 100%                                   |
    Then I should see "Numerical-001"

  @javascript
  Scenario: Create a Numerical question with units
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Numerical" question filling the form with:
      | Question name                      | Numerical-002                               |
      | Question text                      | How many meter is 1m + 20cm + 50mm?         |
      | Default mark                       | 1                                           |
      | General feedback                   | The correct answer is 1.25m                 |
      | id_answer_0                        | 1.25                                        |
      | id_tolerance_0                     | 0                                           |
      | id_fraction_0                      | 100%                                        |
      | id_answer_1                        | 125                                         |
      | id_tolerance_1                     | 0                                           |
      | id_fraction_1                      | 0%                                          |
      | id_answer_2                        | 1250                                        |
      | id_tolerance_2                     | 0                                           |
      | id_fraction_2                      | 0%                                          |
      | id_unitrole                        | The unit must be given, and will be graded. |
      | id_unitpenalty                     | 0.15                                        |
      | id_unitgradingtypes                | as a fraction (0-1) of the question grade   |
      | id_unitsleft                       | on the right, for example 1.00cm or 1.00km  |
      | id_multichoicedisplay              | a drop-down menu                            |
      | id_unit_0                          | m                                           |
    Then I should see "Numerical-002"
    # Checking that the next new question form displays user preferences settings.
    And I press "Create a new question ..."
    And I set the field "item_qtype_numerical" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And the following fields match these values:
      | id_unitrole                        | The unit must be given, and will be graded. |
      | id_unitpenalty                     | 0#15                                        |
      | id_unitgradingtypes                | as a fraction (0-1) of the question grade   |
      | id_multichoicedisplay              | a drop-down menu                            |
      | id_unitsleft                       | on the right, for example 1.00cm or 1.00km  |
