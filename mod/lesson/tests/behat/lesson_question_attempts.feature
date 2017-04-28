@mod @mod_lesson
Feature: In a lesson activity, students can not re-attempt a question more than the allowed amount
  In order to check a lesson question can not be attempted more than the allowed amount
  As a student I need to check I cannot reattempt a question more than I should be allowed

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
    And I add a "Lesson" to section "1"
    And I set the following fields to these values:
      | Name | Test lesson name |
      | Description | Test lesson description |
      | Re-takes allowed | Yes |
      | Minimum number of questions | 3 |
    And I press "Save and return to course"
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
      | Page title | Third page name |
      | Page contents | Third page contents |
      | id_answer_editor_0 | Previous page |
      | id_jumpto_0 | Previous page |
      | id_answer_editor_1 | Next page |
      | id_jumpto_1 | Next page |
    And I press "Save page"
    And I select "Question" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 3 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
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
    And I select "Question" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | Kermit is a frog |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I select "Question" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | The earth is round. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out

  Scenario: Check that we can leave a quiz and when we re-enter we can not re-attempt the question again
    Given I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "The earth is round"
    And I set the following fields to these values:
      | False| 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "Do you want to start at the last page you saw?"
    And I click on "No" "link" in the "#page-content" "css_element"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "The earth is round"
    And I set the following fields to these values:
      | False| 1 |
    When I press "Submit"
    Then I should see "Maximum number of attempts reached - Moving to next page"

  Scenario: Check that we can move past a question we don't want to re-attempt
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Provide option to try a question again" to "Yes"
    And I set the field "Maximum number of attempts" to "3"
    And I press "Save and display"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "The earth is round"
    And I set the following fields to these values:
      | False| 1 |
    And I press "Submit"
    And I should see "Wrong"
    When I press "No, I just want to go on to the next question"
    Then I should not see "The earth is round"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I press "Yes, I'd like to try again"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I press "Yes, I'd like to try again"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should not see "Yes, I'd like to try again"
    And I should see "Continue"

  @javascript @_bug_phantomjs
  Scenario: Check that we can not click back on the browser at the last quiz result page and re-attempt the last question to get full marks
    Given I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "The earth is round"
    And I set the following fields to these values:
      | True| 1 |
    And I press "Submit"
    And I should see "Correct"
    And I press "Continue"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | True| 1 |
    And I press "Submit"
    And I should see "Correct"
    And I press "Continue"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Paper is made from trees"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I press "Continue"
    And I should see "Third page contents"
    And I press "Next page"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 2 (out of 3)"
    And I press the "back" button in the browser
    And I press the "back" button in the browser
    And I press the "back" button in the browser
    And I should see "Paper is made from trees"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I should see "Correct"
    And I press "Continue"
    And I should see "Third page contents"
    When I press "Next page"
    Then I should see "Number of questions answered: 1 (You should answer at least 3)"

  @javascript
  Scenario: Check that we can not click back on the browser and re-attempt a question
    Given I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "The earth is round"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I press the "back" button in the browser
    And I set the following fields to these values:
      | True | 1 |
    When I press "Submit"
    Then I should see "Maximum number of attempts reached - Moving to next page"
    And I press "Continue"
    And I should see "Kermit is a frog"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Wrong"
    And I press the "back" button in the browser
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I should see "Maximum number of attempts reached - Moving to next page"
    And I press "Continue"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Paper is made from trees"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I should see "Correct"
    And I press the "back" button in the browser
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I should see "Maximum number of attempts reached - Moving to next page"
    And I press "Continue"
    And I should see "Third page contents"
    And I press "Next page"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 3)"
