@mod @mod_lesson
Feature: Teachers can review student progress on all lessons in a course by viewing the complete report
  As a Teacher
  I need to view the complete report for one of my students.

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
      | activity   | name             | intro                   | course | idnumber    | retake |
      | lesson     | Test lesson name | Test lesson description | C1     | lesson1     | 1      |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"

  Scenario: View student progress for lesson that was never attempted
    Given I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a question page" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | Paper is made from trees. |
      | id_answer_editor_0 | True |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | False |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    When I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 1"
    And I follow "Complete report"
    Then I should see "No attempts have been made on this lesson"

  Scenario: View student progress for an incomplete lesson containing both content and question pages
    Given I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a question page" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
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
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 1"
    And I follow "Complete report"
    And I should see "Lesson has been started, but not yet completed"
    And I should see "1" in the ".cell.c1" "css_element"
    And I should see "0" in the ".cell.c2" "css_element"

  Scenario: View student progress for a lesson containing both content and question pages
    Given I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a question page" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
      | Page contents | The sky is Pink. |
      | id_answer_editor_0 | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0 | Next page |
      | id_answer_editor_1 | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1 | This page |
    And I press "Save page"
    And I select "Add a question page" from the "qtype" singleselect
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title | True/false question 1 |
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
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "Next page"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "The sky is Pink."
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 1"
    And I follow "Complete report"
    And I should see "Grade: 50.00 / 100.00"
    And I should see "4" in the ".cell.c1" "css_element"
    And I should see "2" in the ".cell.c2" "css_element"
    And I should see "1" in the ".cell.c3" "css_element"

  Scenario: View student attempts in a lesson containing only content pages
    Given I follow "Add a content page"
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
      | id_answer_editor_1 | End of lesson |
      | id_jumpto_1 | End of lesson |
    And I press "Save page"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "End of lesson"
    And I log out
    Then I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 1"
    And I follow "Complete report"
    And I should see "Completed"
    And I should see "2" in the ".cell.c1" "css_element"
    And I should see "0" in the ".cell.c2" "css_element"
    And I should see "0" in the ".cell.c3" "css_element"
