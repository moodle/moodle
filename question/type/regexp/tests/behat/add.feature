@qtype @qtype_regexp
Feature: Test creating a Regexp question
  As a teacher
  In order to test my students
  I need to be able to create a Regexp question

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
  Scenario: Create a Regexp question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I add a "Regular expression short answer" question filling the form with:
      | Question name        | regularshortanswer-001                    |
      | Question text        | What are the colors of the French flag?   |
      | General feedback     | The colours are blue, white and red       |
      | Default mark         | 1                                         |
      | Case sensitivity     | Yes, case must match                      |
      | id_answer_0          | it's blue, white and red                  |
      | id_fraction_0        | 100%                                      |
      | id_feedback_0        | Well done.                                |
      # The pipe character must be escaped
      | id_answer_1          | it's blue, white(,\| and) red             |
      | id_fraction_1        | 100%                                      |
      | id_feedback_1        | Well done too.                            |
      | id_answer_2          | it's [[_blue_, _white_(,\| and) _red_]]   |
      | id_fraction_2        | 100%                                      |
      | id_feedback_2        | Well done too.                            |
    Then I should see "regularshortanswer-001"
