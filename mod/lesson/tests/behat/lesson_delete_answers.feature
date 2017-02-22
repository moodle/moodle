@mod @mod_lesson
Feature: In a lesson activity, teacher can delete question answers and
branch table contents
  In order to modify an existing lesson
  As a teacher
  I need to question answers and branch table contents in the lesson

  Background:
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
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson name |
      | Description | Test lesson description |
    And I follow "Test lesson name"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | Previous page |
      | id_jumpto_1 | Previous page |
    And I press "Save page"
    And I select "Question" from the "qtype" singleselect
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
      | id_jumpto_1 | First page name |
      | id_score_1 | 0 |
    And I press "Save page"
    And I follow "Expanded"

  Scenario: Edit lesson content page
    Given I click on "//th[normalize-space(.)='First page name']/descendant::a[2]" "xpath_element"
    When I set the following fields to these values:
      | id_answer_editor_1 | |
    And I press "Save page"
    And I should not see "Previous page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I should not see "Previous page"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 2 |
    And I press "Submit"
    And I should see "Correct answer"
    And I should not see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 1)."

  Scenario: Edit lesson question page
    Given I click on "//th[normalize-space(.)='Hardest question ever']/descendant::a[2]" "xpath_element"
    When I set the following fields to these values:
      | id_answer_editor_1 | |
    And I press "Save page"
    And I should not see "Incorrect answer"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 1 |
    And I press "Submit"
    And I should not see "Incorrect answer"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 0 (out of 1)."
