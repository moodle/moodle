@qformat @qformat_webct
Feature: Test importing questions from WebCT format.
  In order to reuse questions from am obsolete commercial LMS
  As an teacher
  I need to be able to import them in WebCT format.

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
  Scenario: import some WebCT questions
    When I navigate to "Import" node in "Course administration > Question bank"
    And I set the field "id_format_webct" to "1"
    And I upload "question/format/webct/tests/fixtures/sample_webct.txt" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 6 questions from file"
    And I should see "What's between orange and green in the spectrum?"
    When I press "Continue"
    Then I should see "USER-3"
