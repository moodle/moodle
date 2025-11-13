@ou @ou_vle @qtype @qtype_oumultiresponse
Feature: Test creating an OU multiple response question
  As a teacher
  In order to test my students
  I need to be able to create an OU multiple response question

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

  @javascript
  Scenario: Create an OU multiple response question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "item_qtype_oumultiresponse" question filling the form with:
      | Question name             | OU multiple response 001           |
      | Question text             | Find the capital cities in Europe. |
      | General feedback          | Berlin, Paris and London           |
      | Default mark              | 5                                  |
      | Shuffle the choices?      | 0                                  |
      | Number the choices?       | 1., 2., 3., ...                    |
      | Show standard instruction | Yes                                |
      | Choice 2                  | Spain                              |
      | Choice 3                  | London                             |
      | Choice 4                  | Barcelona                          |
      | Choice 5                  | Paris                              |
      | id_correctanswer_0        | 0                                  |
      | id_correctanswer_1        | 0                                  |
      | id_correctanswer_2        | 1                                  |
      | id_correctanswer_3        | 0                                  |
      | id_correctanswer_4        | 1                                  |
      | Hint 1                    | First hint                         |
      | Hint 2                    | Second hint                        |
    Then I should see "OU multiple response 001"
    # Checking that the next new question form displays user preferences settings.
    And I press "Create a new question ..."
    And I set the field "item_qtype_oumultiresponse" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And the following fields match these values:
      | Default mark              | 5               |
      | Shuffle the choices?      | 0               |
      | Number the choices?       | 1., 2., 3., ... |
      | Show standard instruction | Yes             |
