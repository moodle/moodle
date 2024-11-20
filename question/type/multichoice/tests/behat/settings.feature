@qtype @qtype_multichoice @javascript
Feature: Test settings for Multiple choice question
  As an admininstrator
  In order to provide default settings for commonly used fields in Multiple choice questions
  I need to be able to edit side-wide settings, so teachers have less fields to setup for their first time.

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

  Scenario: Testing the settings for qtype_multichoice
    Given I log in as "admin"
    When I navigate to "Plugins > Question types > Multiple choice" in site administration
    Then the following fields match these values:
      |id_s_qtype_multichoice_answerhowmany           | One answer only |
      |id_s_qtype_multichoice_shuffleanswers          | 1               |
      |id_s_qtype_multichoice_answernumbering         | a., b., c., ... |
      |id_s_qtype_multichoice_showstandardinstruction |                 |
    And I set the following fields to these values:
      |id_s_qtype_multichoice_shuffleanswers          |                 |
      |id_s_qtype_multichoice_answernumbering         | 1., 2., 3., ... |
      |id_s_qtype_multichoice_showstandardinstruction | 1               |
    And I press "Save changes"
    And I log out
    And I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Multiple choice" question filling the form with:
      | Question name              | Multi-choice-001                       |
      | Question text              | Find the capital city of England.      |
      | General feedback           | London is the capital city of England. |
      | Default mark               | 5                                      |
      | Choice 1                   | Manchester                             |
      | Choice 2                   | Buckingham                             |
      | Choice 3                   | London                                 |
      | Choice 4                   | Barcelona                              |
      | Choice 5                   | Paris                                  |
      | id_fraction_0              | None                                   |
      | id_fraction_1              | None                                   |
      | id_fraction_2              | 100%                                   |
      | id_fraction_3              | None                                   |
      | id_fraction_4              | None                                   |
      | Hint 1                     | First hint                             |
      | Hint 2                     | Second hint                            |
    And I should see "Multi-choice-001"
    And I press "Create a new question ..."
    And I set the field "Multiple choice" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And the following fields match these values:
      | One or multiple answers?   | One answer only |
      | Shuffle the choices?       |                 |
      | Number the choices?        | 1., 2., 3., ... |
      | Show standard instructions | Yes             |
