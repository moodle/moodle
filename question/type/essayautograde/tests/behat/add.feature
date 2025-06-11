@qtype @qtype_essayautograde
Feature: Test creating an Essay autograde question
  As a teacher
  In order to test my students
  I need to be able to create an Essay autograde question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  Scenario: Create an Essay autograde question with Response format set to 'HTML editor'
    When I add a "Essay (auto-grade)" question filling the form with:
      | Question name            | essayautograde-001                      |
      | Question text            | Write an essayautograde with 500 words. |
      | General feedback         | This is general feedback                |
      | Response format          | HTML editor                             |
    Then I should see "essayautograde-001"

  Scenario: Create an Essay autograde question with Response format set to 'HTML editor with file picker'
    When I add a "Essay (auto-grade)" question filling the form with:
      | Question name            | essayautograde-002                      |
      | Question text            | Write an essayautograde with 500 words. |
      | General feedback         | This is general feedback                |
      | Response format          | HTML editor with file picker            |
    Then I should see "essayautograde-002"

  Scenario: Create an Essay autograde question with Response format set to 'plain'
    When I add a "Essay (auto-grade)" question filling the form with:
      | Question name            | essayautograde-003                      |
      | Question text            | Write an essayautograde with 500 words. |
      | General feedback         | This is general feedback                |
      | Response format          | Plain                                   |
    Then I should see "essayautograde-003"

  Scenario: Create an Essay autograde question with Response format set to 'monospaced'
    When I add a "Essay (auto-grade)" question filling the form with:
      | Question name            | essayautograde-004                      |
      | Question text            | Write an essayautograde with 500 words. |
      | General feedback         | This is general feedback                |
      | Response format          | Plain text, monospaced font             |
    Then I should see "essayautograde-004"
