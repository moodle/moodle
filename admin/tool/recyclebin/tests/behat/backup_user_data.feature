@tool @tool_recyclebin
Feature: Backup user data
  As a teacher
  I want user data to be backed up when I delete a course module
  So that I can recover student content

  Background: Course with teacher and student exist.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher@asd.com |
      | student1 | Student | 1 | student@asd.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | coursebinenable | 1 | tool_recyclebin |
      | autohide | 0 | tool_recyclebin |

  @javascript
  Scenario: Delete and restore a quiz with user data
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name        | Quiz 1                |
      | Description | Test quiz description |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name                      | TF1                          |
      | Question text                      | First question               |
      | General feedback                   | Thank you, this is the general feedback |
      | Correct answer                     | False                                   |
      | Feedback for the response 'True'.  | So you think it is true                 |
      | Feedback for the response 'False'. | So you think it is false                |
    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name                      | TF2                                     |
      | Question text                      | Second question                         |
      | General feedback                   | Thank you, this is the general feedback |
      | Correct answer                     | False                                   |
      | Feedback for the response 'True'.  | So you think it is true                 |
      | Feedback for the response 'False'. | So you think it is false                |
    And I log out
    When I am on the "Quiz 1" "quiz activity" page logged in as student1
    And I press "Attempt quiz"
    And I click on "True" "radio" in the "First question" "question"
    And I click on "False" "radio" in the "Second question" "question"
    And I press "Finish attempt"
    And I press "Submit all and finish"
    And I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    And I should see "5.00 out of 10.00"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I delete "Quiz 1" activity
    And I run all adhoc tasks
    And I navigate to "Recycle bin" in current page administration
    And I should see "Quiz 1"
    And I click on "Restore" "link" in the "region-main" "region"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I navigate to "User report" in the course gradebook
    Then "Quiz 1" row "Grade" column of "user-grade" table should contain "5"
    And "Quiz 1" row "Percentage" column of "user-grade" table should contain "50"
