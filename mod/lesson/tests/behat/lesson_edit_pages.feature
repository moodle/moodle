@mod @mod_lesson
Feature: In a lesson activity, teacher can edit lesson's pages
  In order to modify an existing lesson
  As a teacher
  I need to edit pages in the lesson

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
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
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

  Scenario: Edit lesson content page
    Given I click on "//th[normalize-space(.)='Second page name']/descendant::a[2]" "xpath_element"
    When I set the following fields to these values:
      | Page title | Modified second page |
      | Page contents | Modified contents |
      | id_answer_editor_0 | Forward |
      | id_jumpto_0 |Next page |
      | id_answer_editor_1 | Backward |
      | id_jumpto_1 | Previous page |
    And I press "Save page"
    Then I should see "Modified second page"
    And I should not see "Second page name"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Modified contents"
    And I should not see "Second page contents"
    And I press "Backward"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Modified contents"
    And I press "Forward"
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
      | Page title | New hardest question |
      | Page contents | 1 + 2? |
      | id_answer_editor_0 | 2 |
      | id_response_editor_0 | Your answer is incorrect |
      | id_jumpto_0 | End of lesson |
      | id_score_0 | 0 |
      | id_answer_editor_1 | 3 |
      | id_response_editor_1 | Your answer is correct |
      | id_jumpto_1 | End of lesson |
      | id_score_1 | 1 |
    And I press "Save page"
    Then I should see "New hardest question"
    And I should not see "Hardest question ever"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "1 + 2?"
    And I should not see "1 + 1?"
    And I set the following fields to these values:
      | Your answer | 3 |
    And I press "Submit"
    And I should see "Your answer is correct"
    And I should not see "Incorrect answer"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 1)."
