@mod @mod_lesson
Feature: In Dashboard, a student can see their current status on all lessons with an upcoming due date
  In order to know my status on a lesson
  As a student
  I need to see it in Dashboard

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
    And the following "activities" exist:
      | activity | name             | intro                   | deadline   | retake | course | idnumber |
      | lesson   | Test lesson name | Test lesson description | 1893481200 | 1      | C1     | lesson1  |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

  @javascript
  Scenario: A completed lesson with only questions that allows multiple attempts
    Given I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | Cat is an amphibian |
      | id_answer_editor_0 | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I set the field "qtype" to "Question"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should see "You have lessons that are due"
    And I click on ".collapsibleregioncaption" "css_element"
    And I should see "Completed, You can re-attempt this lesson"

  @javascript
  Scenario: A completed lesson with only questions that does not allow multiple attempts
    Given  I follow "Test lesson name"
    And I navigate to "Edit settings" node in "Lesson administration"
    And I set the following fields to these values:
      | Re-takes allowed | 0 |
    And I press "Save and display"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | Cat is an amphibian |
      | id_answer_editor_0 | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I set the field "qtype" to "Question"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should not see "You have lessons that are due"

  @javascript
  Scenario: A completed lesson with only content pages that allows multiple attempts
    Given I follow "Test lesson name"
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
      | id_answer_editor_1 | End of lesson |
      | id_jumpto_1 | End of lesson |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "End of lesson"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should see "You have lessons that are due"
    And I click on ".collapsibleregioncaption" "css_element"
    And I should see "Completed, You can re-attempt this lesson"

  @javascript
  Scenario: A completed lesson with only content pages that does not allow multiple attempts
    Given I follow "Test lesson name"
    And I navigate to "Edit settings" node in "Lesson administration"
    And I set the following fields to these values:
      | Re-takes allowed | 0 |
    And I press "Save and display"
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
      | id_answer_editor_1 | End of lesson |
      | id_jumpto_1 | End of lesson |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "End of lesson"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should not see "You have lessons that are due"

  @javascript
  Scenario: An incomplete lesson with only questions.
    Given I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | Cat is an amphibian |
      | id_answer_editor_0 | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I set the field "qtype" to "Question"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should see "You have lessons that are due"
    And I click on ".collapsibleregioncaption" "css_element"
    And I should see "Lesson has been started, but not yet completed"

  @javascript
  Scenario: An incomplete lesson with only content pages.
    Given I follow "Test lesson name"
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
      | id_answer_editor_1 | End of lesson |
      | id_jumpto_1 | End of lesson |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should see "You have lessons that are due"
    And I click on ".collapsibleregioncaption" "css_element"
    And I should see "Lesson has been started, but not yet completed"

  @javascript
  Scenario: A lesson with only questions that has not been started.
    Given I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | Cat is an amphibian |
      | id_answer_editor_0 | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I set the field "qtype" to "Question"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 2 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should see "You have lessons that are due"
    And I click on ".collapsibleregioncaption" "css_element"
    And I should see "No attempts have been made on this lesson"

  @javascript
  Scenario: A lesson with only content pages that has not been started.
    Given I follow "Test lesson name"
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
      | id_answer_editor_1 | End of lesson |
      | id_jumpto_1 | End of lesson |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should see "You have lessons that are due"
    And I click on ".collapsibleregioncaption" "css_element"
    And I should see "No attempts have been made on this lesson"
