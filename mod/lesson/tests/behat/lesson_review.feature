@mod @mod_lesson
Feature: In a lesson activity, students can review the answers they gave to questions
  To review questions of a lesson
  As a student
  I need to complete a lesson answering all of the questions.

  Scenario: Student answers questions and then reviews them.
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    Given the following "activities" exist:
      | activity   | name             | course | idnumber |
      | lesson     | Test lesson name | C1     | lesson1  |
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Display ongoing score | Yes |
      | Slideshow | Yes |
      | Maximum number of answers | 10 |
      | Allow student review | Yes |
      | Maximum number of attempts per question | 3 |
      | Custom scoring | No |
      | Re-takes allowed | Yes |
    And I press "Save and display"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Hardest question ever |
      | Page contents | 1 + 1? |
      | id_answer_editor_0 | 2 |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | 1 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I select "Add a question page" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Next question |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "You have answered 0 correctly out of 0 attempts."
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should see "You have answered 0 correctly out of 1 attempts."
    And I press "Continue"
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I should see "You have answered 1 correctly out of 2 attempts."
    And I press "Continue"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I should see "You have answered 2 correctly out of 3 attempts."
    And I press "Continue"
    When I follow "Review lesson"
    Then I should see "You have answered 2 correctly out of 3 attempts."
    And I press "Next page"
    And I should see "You have answered 2 correctly out of 3 attempts."
    And I press "Continue"
    And I should see "You have answered 2 correctly out of 3 attempts."
    And I press "Next page"
    And I should see "You have answered 2 correctly out of 3 attempts."
