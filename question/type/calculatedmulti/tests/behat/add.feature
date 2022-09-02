@qtype @qtype_calculatedmulti
Feature: Test creating a Calculated multichoice question
  As a teacher
  In order to test my students
  I need to be able to create Calculated multichoice questions

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

  Scenario: Create a Calculated question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Calculated multichoice" to "1"
    And I press "Add"
    And I set the following fields to these values:
      | Question name | calculatedmulti-001                               |
      | Question text | What is the sum of those two numbers: {a} and {b} |
      | Choice 1      | {a}+{b}                                           |
      | Grade         | 100%                                              |
      | Choice 2      | {a}*{b}                                           |
      | Choice 3      | {a}-{b}                                           |
    And I press "id_submitbutton"
    And I should see "Choose wildcards dataset properties"
    And I press "id_submitbutton"
    And I should see "Edit the wildcards datasets"
    And I press "id_addbutton"
    And I set the following fields to these values:
      | id_number_2       | 3.0 |
      | id_number_1       | 4.0 |
    And I press "id_savechanges"
    # Checking that the wildcard values are there
    And I am on the "calculatedmulti-001" "core_question > edit" page logged in as teacher
    And I press "id_submitbutton"
    And I should see "Choose wildcards dataset properties"
    And I press "id_submitbutton"
    Then I should see "3+4"
