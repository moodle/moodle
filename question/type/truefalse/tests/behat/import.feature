@qtype @qtype_truefalse
Feature: Test importing True/False questions
  As a teacher
  In order to reuse True/False questions
  I need to import them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | T1        | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"

  @javascript @_file_upload
  Scenario: import a True/False question.
    When I navigate to "Import" node in "Course administration > Question bank"
    And I set the field "id_format_xml" to "1"
    And I upload "question/type/truefalse/tests/fixtures/testquestion.moodle.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 1 questions from file"
    And I should see "The answer is true."
    And I press "Continue"
    And I should see "true-false-001"
