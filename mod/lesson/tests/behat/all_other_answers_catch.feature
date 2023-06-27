@mod @mod_lesson
Feature: Numeric and short answer questions have a section to catch all other student answers.
  In order for lesson pages to catch any student answer
  As a teacher
  I need to fill in the sections to catch all other student answers

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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Lesson" to section "1"
    And I set the following fields to these values:
      | Name | Test lesson name |
      | Description | Test lesson description |
      | Provide option to try a question again | Yes |
      | Maximum number of attempts | 3 |
    And I press "Save and display"

  Scenario: I can create a numerical question with an option to catch all student responses.
    Given I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Numerical question |
      | Page contents | What is 1 + 2? |
      | id_answer_editor_0 | 3 |
      | id_jumpto_0 | End of lesson |
      | id_enableotheranswers | 1 |
      | id_jumpto_6 | Next page |
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
    And I set the field "Your answer" to "5"
    And I press "Submit"
    And I should see "That's the wrong answer"
    And I press "Yes, I'd like to try again"
    And I should see "What is 1 + 2?"
    And I set the field "Your answer" to "7"
    And I press "Submit"
    And I should see "That's the wrong answer"
    When I press "No, I just want to go on to the next question"
    Then I should see "You are here to move on"

  Scenario: I can create a shortanswer question with an option to catch all student responses.
    Given I follow "Add a question page"
    And I set the field "Select a question type" to "Short answer"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Short answer question |
      | Page contents | Please type in cat |
      | id_answer_editor_0 | 3 |
      | id_jumpto_0 | End of lesson |
      | id_enableotheranswers | 1 |
      | id_jumpto_6 | Next page |
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
    And I set the field "Your answer" to "dog"
    And I press "Submit"
    And I should see "That's the wrong answer"
    And I press "Yes, I'd like to try again"
    And I should see "Please type in cat"
    And I set the field "Your answer" to "bird"
    And I press "Submit"
    And I should see "That's the wrong answer"
    When I press "No, I just want to go on to the next question"
    Then I should see "You are here to move on"
