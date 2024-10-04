@qtype @qtype_calculatedmulti
Feature: Test editing a Calculated multichoice question
    As a teacher
    In order to be able to update my Calculated multichoice questions
    I need to edit them

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
    And the following "user preferences" exist:
      | user    | preference | value    |
      | teacher | htmleditor | textarea |

  Scenario: Add, edit and preview a Calculated multichoice question with HTML in answers
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Calculated multichoice" to "1"
    And I click on "Add" "button"
    And I set the following fields to these values:
      | Question name   | calculatedmulti-001                |
      | Question text   | Multiply those two: s^{A} and s{B} |
      | Choice 1        | s<sup>{={A}*{B}}</sup>             |
      | Choice 1 format | 1                                  |
      | Grade           | 100%                               |
      | Choice 2        | s<sup>{={A}+{B}}</sup>             |
      | Choice 2 format | 1                                  |
    And I press "id_submitbutton"
    And I should see "Choose wildcards dataset properties"
    And I press "id_submitbutton"
    And I should see "Edit the wildcards datasets"
    And I press "id_addbutton"
    And I set the following fields to these values:
      | id_number_2 | 6 |
      | id_number_1 | 4 |
    And I press "id_savechanges"
    # Checking that the wildcard values are there
    And I am on the "calculatedmulti-001" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    And I should see "Choose wildcards dataset properties"
    And I press "id_submitbutton"
    And I press "id_savechanges"
    And I should see "Edited question name"
    # Preview it.
    And I choose "Preview" action for "Edited question name" in the question bank
    Then I should not see "<sup>"

  Scenario: Add, edit and preview a Calculated multichoice question with plain-text answers
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Calculated multichoice" to "1"
    And I click on "Add" "button"
    And I set the following fields to these values:
      | Question name   | calculatedmulti-001                |
      | Question text   | Multiply those two: s^{A} and s{B} |
      | Choice 1        | s<sup>{={A}*{B}}</sup>             |
      | Choice 1 format | 2                                  |
      | Grade           | 100%                               |
      | Choice 2        | s<sup>{={A}+{B}}</sup>             |
      | Choice 2 format | 2                                  |
    And I press "id_submitbutton"
    And I should see "Choose wildcards dataset properties"
    And I press "id_submitbutton"
    And I should see "Edit the wildcards datasets"
    And I press "id_addbutton"
    And I set the following fields to these values:
      | id_number_2 | 6 |
      | id_number_1 | 4 |
    And I press "id_savechanges"
    # Checking that the wildcard values are there
    And I am on the "calculatedmulti-001" "core_question > edit" page logged in as teacher
    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    And I should see "Choose wildcards dataset properties"
    And I press "id_submitbutton"
    And I press "id_savechanges"
    And I should see "Edited question name"
    # Preview it.
    And I choose "Preview" action for "Edited question name" in the question bank
    Then I should see "<sup>"
