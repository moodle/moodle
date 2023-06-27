@mod @mod_lesson
Feature: In a lesson activity, if custom scoring is not enabled, student should see
  some informations at the end of lesson: questions answered, correct answers, grade, score

  Scenario: Informations at end of lesson if custom scoring not enabled
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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson name |
      | Description | Test lesson description |
      | Custom scoring | No |
    And I follow "Test lesson name"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a question page" from the "qtype" singleselect
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
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Test lesson name"
    Then I should see "First page contents"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Number of questions answered: 1"
    And I should see "Number of correct answers: 0"
    And I should see "Your score is 0 (out of 1)."
    And I should see "Your current grade is 0.0 out of 100"
