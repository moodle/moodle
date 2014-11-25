@mod @mod_lesson
Feature: In a lesson activity, students can navigate through a series of pages in various ways depending upon their answers to questions
  In order to create a lesson with conditional paths
  As a teacher
  I need to add pages and questions with links between them

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

  @javascript
  Scenario: Student navigation with pages and questions
    Given I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson name |
      | Description | Test lesson description |
    And I follow "Test lesson name"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I set the field "qtype" to "Add a content page"
    And I set the following fields to these values:
      | Page title | Second page name |
      | Page contents | Second page contents |
      | id_answer_editor_0 | Previous page |
      | id_jumpto_0 | Previous page |
      | id_answer_editor_1 | Next page |
      | id_jumpto_1 | Next page |
    And I press "Save page"
    And I follow "Expanded"
    And I click on "Add a question page here" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' addlinks ')][3]" "xpath_element"
    And I set the field "Select a question type" to "Numerical"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Hardest question ever |
      | Page contents | 1 + 1? |
      | id_answer_editor_0 | 2 |
      | id_response_editor_0 | Correct answer |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 1 |
      | id_answer_editor_1 | 1 |
      | id_response_editor_1 | Incorrect answer |
      | id_jumpto_1 | Second page name |
      | id_score_1 | 0 |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    When I follow "Test lesson name"
    Then I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I should not see "First page contents"
    And I press "Previous page"
    And I should see "First page contents"
    And I should not see "Second page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should see "Incorrect answer"
    And I press "Continue"
    And I should see "Second page name"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I should see "Correct answer"
    And I should not see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 0 (out of 1)."

  @javascript
  Scenario: Student reattempts a question until out of attempts
    Given I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson name |
      | Description | Test lesson description |
      | id_review | Yes |
      | id_maxattempts | 3 |
    And I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the following fields to these values:
      | id_qtype | True/false |
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | Test question |
      | Page contents | Test content |
      | id_answer_editor_0 | right |
      | id_answer_editor_1 | wrong |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    When I follow "Test lesson name"
    Then I should see "Test content"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should see "You have 2 attempt(s) remaining"
    And I press "Yes, I'd like to try again"
    And I should see "Test content"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should see "You have 1 attempt(s) remaining"
    And I press "Yes, I'd like to try again"
    And I should see "Test content"
    And I set the following fields to these values:
      | wrong | 1 |
    And I press "Submit"
    And I should see "(Maximum number of attempts reached - Moving to next page)"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
