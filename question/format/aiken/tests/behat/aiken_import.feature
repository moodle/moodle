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
    And I log in as "teacher"
    And I am on "Course 1" course homepage

@javascript @_file_upload
  Scenario: import some Aiken questions
    When I navigate to "Question bank > Import" in current page administration
    And I set the field "id_format_aiken" to "1"
    And I upload "question/format/aiken/tests/fixtures/questions.aiken.txt" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 2 questions from file"
    And I should see "The Moodle project was started by:"
    And I press "Continue"
    And I should see "The Moodle project was started by:"
