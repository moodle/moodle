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
      | activity   | name               | intro                     | course | section | idnumber |
      | lesson     | Test lesson name   | Test lesson description   | C1     | 1       | lesson1  |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Provide option to try a question again | Yes |
      | Maximum number of attempts per question | 2 |
    And I press "Save and return to course"
    And I follow "Test lesson name"

  Scenario: A student answering incorrectly to a question will see an option to move to the next question if set up.
    Given I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Numerical question |
      | Page contents | What is 1 + 2? |
      | id_answer_editor_0 | 3 |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | 2 |
      | id_jumpto_1 | Next page |
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
    And I set the following fields to these values:
      | Page title | Just move on page |
      | Page contents | You are here to move on |
      | id_answer_editor_0 | End this lesson |
      | id_jumpto_0 | End of lesson |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    When I set the field "Your answer" to "2"
    And I press "Submit"
    And I should see "That's the wrong answer"
    And I should see "No, I just want to go on to the next question"
    And I press "No, I just want to go on to the next question"
    Then I should see "You are here to move on"

  Scenario: A student answering incorrectly to a question will only see an option to try again if there is no matching wrong response.
    Given I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Numerical question |
      | Page contents | What is 1 + 2? |
      | id_answer_editor_0 | 3 |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
    And I set the following fields to these values:
      | Page title | Just move on page |
      | Page contents | You are here to move on |
      | id_answer_editor_0 | End this lesson |
      | id_jumpto_0 | End of lesson |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    When I set the field "Your answer" to "2"
    And I press "Submit"
    And I should see "That's the wrong answer"
    Then I should not see "No, I just want to go on to the next question"
    And I press "Yes, I'd like to try again"
    And I should see "What is 1 + 2?"
