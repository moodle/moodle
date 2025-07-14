@mod @mod_lesson
Feature: An incorrect response to an answer with multiple attempts show appropriate continue buttons
  In order for lesson the appropriate continue button to be displayed
  As a teacher
  I need to create a lesson with multiple attempts for each question

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity   | name               | course | idnumber |
      | lesson     | Test lesson name   | C1     | lesson1  |
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Provide option to try a question again | Yes |
      | Maximum number of tries per question | 2 |
    And I press "Save and display"

  Scenario: A student answering incorrectly to a question will see an option to move to the next question if set up.
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype   | title              | content                 |
      | Test lesson name | numeric | Numerical question | What is 1 + 2?          |
      | Test lesson name | content | Just move on page  | You are here to move on |
    And the following "mod_lesson > answers" exist:
      | page               | answer          | jumpto        | score |
      | Numerical question | 3               | Next page     | 1     |
      | Numerical question | 2               | Next page     | 0     |
      | Just move on page  | End this lesson | End of lesson | 0     |
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    When I set the field "Your answer" to "2"
    And I press "Submit"
    And I should see "That's the wrong answer"
    And I should see "No, I just want to go on to the next question"
    And I press "No, I just want to go on to the next question"
    Then I should see "You are here to move on"

  Scenario: A student answering incorrectly to a question will only see an option to try again if there is no matching wrong response.
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype   | title              | content                 |
      | Test lesson name | numeric | Numerical question | What is 1 + 2?          |
      | Test lesson name | content | Just move on page  | You are here to move on |
    And the following "mod_lesson > answers" exist:
      | page               | answer          | jumpto        | score |
      | Numerical question | 3               | Next page     | 1     |
      | Just move on page  | End this lesson | End of lesson | 0     |
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    When I set the field "Your answer" to "2"
    And I press "Submit"
    And I should see "That's the wrong answer"
    Then I should not see "No, I just want to go on to the next question"
    And I press "Yes, I'd like to try again"
    And I should see "What is 1 + 2?"
