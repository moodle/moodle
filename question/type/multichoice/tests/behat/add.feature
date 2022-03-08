@qtype @qtype_multichoice
Feature: Test creating a Multiple choice question
  As a teacher
  In order to test my students
  I need to be able to create a Multiple choice question

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

  Scenario: Create a Multiple choice question with multiple response
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Multiple choice" question filling the form with:
      | Question name            | Multi-choice-001                   |
      | Question text            | Find the capital cities in Europe. |
      | General feedback         | Paris and London                   |
      | One or multiple answers? | Multiple answers allowed           |
      | Choice 1                 | Tokyo                              |
      | Choice 2                 | Spain                              |
      | Choice 3                 | London                             |
      | Choice 4                 | Barcelona                          |
      | Choice 5                 | Paris                              |
      | id_fraction_0            | None                               |
      | id_fraction_1            | None                               |
      | id_fraction_2            | 50%                                |
      | id_fraction_3            | None                               |
      | id_fraction_4            | 50%                                |
      | Hint 1                   | First hint                         |
      | Hint 2                   | Second hint                        |
    Then I should see "Multi-choice-001"

  @javascript
  Scenario: Create a Multiple choice question with single response
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Multiple choice" question filling the form with:
      | Question name              | Multi-choice-002                       |
      | Question text              | Find the capital city of England.      |
      | General feedback           | London is the capital city of England. |
      | Default mark               | 5                                      |
      | One or multiple answers?   | One answer only                        |
      | Shuffle the choices?       | 0                                      |
      | Number the choices?        | 1., 2., 3., ...                        |
      | Show standard instructions | Yes                                    |
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
    Then I should see "Multi-choice-002"
    # Checking that the next new question form displays user preferences settings.
    And I press "Create a new question ..."
    And I set the field "Multiple choice" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And the following fields match these values:
      | Default mark               | 5                 |
      | One or multiple answers?   | One answer only   |
      | Shuffle the choices?       | 0                 |
      | Number the choices?        | 1., 2., 3., ...   |
      | Show standard instructions | Yes               |
