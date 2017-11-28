@qtype @qtype_truefalse
Feature: Test creating a True/False question
  As a teacher
  In order to test my students
  I need to be able to create a True/False question

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
    And I navigate to "Question bank" node in "Course administration"

  Scenario: Create a True/False question with Correct answer as False
    When I add a "True/False" question filling the form with:
      | Question name                      | true-false-001                             |
      | Question text                      | Manchester is the capital city of England. |
      | Default mark                       | 1                                          |
      | General feedback                   | London is the capital city of England.     |
      | Correct answer                     | False                                      |
      | Feedback for the response 'True'.  | Well done!                                 |
      | Feedback for the response 'False'. | Read more about England.                   |
    Then I should see "true-false-001"

  Scenario: Create a True/False question with Correct answer as True
    When I add a "True/False" question filling the form with:
      | Question name                      | true-false-002                         |
      | Question text                      | London is the capital city of England. |
      | Default mark                       | 1                                      |
      | General feedback                   | London is the capital city of England. |
      | Correct answer                     | True                                   |
      | Feedback for the response 'True'.  | Well done!                             |
      | Feedback for the response 'False'. | Read more about England.               |
    Then I should see "true-false-002"
