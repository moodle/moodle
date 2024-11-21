@qformat @qformat_h5p
Feature: Test importing questions from H5P content type.
  In order to reuse H5P content as questions
  As an teacher
  I need to be able to import them from h5p file

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
  Scenario: import .h5p file of Question Set content
    When I am on the "Course 1" "core_question > course question import" page logged in as teacher
    And I set the field "id_format_h5p" to "1"
    And I upload "question/format/h5p/tests/fixtures/question-set-616.h5p" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 3 questions from file"
    And I should see "Which one of the following berries are red"
    When I press "Continue"
    Then I should see "Which one of the following berries are red"
