@qformat @qformat_webct
Feature: Test importing calculated question from WebCT format.
  In order to reuse calculated questions from a commercial LMS
  As a teacher
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
  Scenario: import a WebCT calculated question
    When I navigate to "Question bank > Import" in current page administration
    And I set the field "id_format_webct" to "1"
    And I upload "question/format/webct/tests/fixtures/sample_calculated_webct.txt" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 1 questions from file"
    And I should see "Find the area in m2 of a square with sides of length {l} m."
    When I press "Continue"
    Then I should see "calculated: Square area"
