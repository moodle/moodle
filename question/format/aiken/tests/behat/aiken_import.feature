@qformat @qformat_aiken
Feature: Test importing questions from Aiken format.
  In order to reuse questions
  As an teacher
  I need to be able to import them in Aiken format.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teacher   |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  @javascript @_file_upload
  Scenario: import some Aiken questions
    Given I am on the "Course 1" "core_question > course question import" page logged in as "teacher"
    And I set the field "id_format_aiken" to "1"
    And I upload "question/format/aiken/tests/fixtures/questions.aiken.txt" file to "Import" filemanager
    When I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 2 questions from file"
    And I should see "The Moodle project was started by:"
    And I press "Continue"
    And I should see "The Moodle project was started by:"
