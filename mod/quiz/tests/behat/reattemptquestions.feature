@mod @mod_quiz
Feature: Add a quiz
  In order to allow students re-attempting graded question
  As a teacher
  I need to create a quiz, set 'Restart question' field to 'Yes', add questions to the quiz which can be graded automatically.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
      | student1 | S1        | Student1 | student1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

    And I add a "Quiz" to section "1" and I fill the form with:
      | Name                     | Quiz 1             |
      | Description              | Quiz 1 description |
      | How questions behave     | Immediate feedback |
      | Restart graded questions | Yes                |

    And I add a "True/False" question to the "Quiz 1" quiz with:
      | Question name                      | TF001                                   |
      | Question text                      | Answer question TF001                   |
      | General feedback                   | Thank you, this is the general feedback |
      | Correct answer                     | False                                   |
      | Feedback for the response 'True'.  | So you think it is true                 |
      | Feedback for the response 'False'. | So you think it is false                |
    And I log out

  @javascript
  Scenario: Log in as a student, attempt the quiz and checking whether you can re-attempt a graded question in the appropriate behaviour settings
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Quiz 1"
    And I press "Attempt quiz now"
    Then I should see "TF001"
    And I should see "Answer question TF001"
    And I set the field "True" to "1"
    And I press "Check"
    And I should see "Incorrect"
    Then I press "Restart question"
    And I should see "Not complete"
    And I set the field "False" to "1"
    And I press "Check"
    And I should see "Correct"
