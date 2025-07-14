@mod @mod_lesson
Feature: Set the maximum number of attempts for lesson activity question
  In order to limit the number of attempts a student can take for lesson activity question
  As a teacher
  I should be able to set the maximum number of attempts for a lesson activity question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario: Lesson activity question maximum number of attempts can be set
    Given the following "activities" exist:
      | activity   | name             | course | modattempts | review | maxattempts | feedback |
      | lesson     | Test lesson name | C1     | 0           | 1      | 2           | 1        |
    And the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title      | content               |
      | Test lesson name | truefalse | Question 1 | Dolphins are mammals. |
      | Test lesson name | truefalse | Question 2 | Trees are plants.     |
    And the following "mod_lesson > answers" exist:
      | page       | answer | response | jumpto    | score |
      | Question 1 | True   | Right    | Next page | 1     |
      | Question 1 | False  | Wrong    | This page | 0     |
      | Question 2 | True   | Right    | Next page | 1     |
      | Question 2 | False  | Wrong    | This page | 0     |
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    # Answer lesson activity question 1 incorrectly.
    When I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    # Confirm you can still re-attempt the question 1.
    Then I should see "You have 1 attempt(s) remaining"
    And I press "Yes, I'd like to try again"
    # Answer question 1 incorrectly again.
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    # Confirm you can't re-attempt the question 1 anymore.
    And I should not see "Yes, I'd like to try again"
    And I press "Continue"
    # Answer question 2 correctly.
    And I set the following fields to these values:
      | True  | 1 |
    And I press "Submit"
    And I should not see "Yes, I'd like to try again"
    # Complete attempt.
    And I press "Continue"
    And I am on the "Test lesson name" "lesson activity" page
    # Confirm you can't see the question anymore.
    And I should see "You are not allowed to retake this lesson."
