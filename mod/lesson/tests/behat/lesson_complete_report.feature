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
      | activity   | name             | course | idnumber    | retake |
      | lesson     | Test lesson name | C1     | lesson1     | 1      |

  Scenario: View student progress for lesson that was never attempted
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | content   | First page name       | First page contents       |
      | Test lesson name | truefalse | True/false question 1 | Paper is made from trees. |
    And the following "mod_lesson > answers" exist:
      | page                  | answer    | response | jumpto    | score |
      | First page name       | Next page |          | Next page | 0     |
      | True/false question 1 | True      | Correct  | Next page | 1     |
      | True/false question 1 | False     | Wrong    | This page | 0     |
    And I log in as "teacher1"
    When I am on "Course 1" course homepage
    And I navigate to course participants
    And I follow "Student 1"
    And I follow "Complete report"
    Then I should see "No attempts have been made on this lesson"

  Scenario: View student progress for an incomplete lesson containing both content and question pages
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | content   | First page name       | First page contents       |
      | Test lesson name | content   | Second page name      | Second page contents      |
      | Test lesson name | truefalse | True/false question 1 | Paper is made from trees. |
    And the following "mod_lesson > answers" exist:
      | page                  | answer    | response | jumpto    | score |
      | First page name       | Next page |          | Next page | 0     |
      | Second page name      | Next page |          | Next page | 0     |
      | True/false question 1 | True      | Correct  | Next page | 1     |
      | True/false question 1 | False     | Wrong    | This page | 0     |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    Then I am on the "Course 1" course page logged in as teacher1
    And I navigate to course participants
    And I follow "Student 1"
    And I follow "Complete report"
    And I should see "Lesson has been started, but not yet completed"
    And I should see "1" in the ".cell.c1" "css_element"
    And I should see "0" in the ".cell.c2" "css_element"

  Scenario: View student progress for a lesson containing both content and question pages
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | content   | First page name       | First page contents       |
      | Test lesson name | content   | Second page name      | Second page contents      |
      | Test lesson name | truefalse | True/false question 1 | Paper is made from trees. |
      | Test lesson name | truefalse | True/false question 2 | The sky is Pink.          |
    And the following "mod_lesson > answers" exist:
      | page                  | answer        | response | jumpto        | score |
      | First page name       | Next page     |          | Next page     | 0     |
      | Second page name      | Previous page |          | Previous page | 0     |
      | Second page name      | Next page     |          | Next page     | 0     |
      | True/false question 1 | True          | Correct  | Next page     | 1     |
      | True/false question 1 | False         | Wrong    | This page     | 0     |
      | True/false question 2 | False         | Correct  | Next page     | 1     |
      | True/false question 2 | True          | Wrong    | This page     | 0     |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
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
    Then I am on the "Course 1" course page logged in as teacher1
    And I navigate to course participants
    And I follow "Student 1"
    And I follow "Complete report"
    And I should see "Grade: 50.00 / 100.00"
    And I should see "4" in the ".cell.c1" "css_element"
    And I should see "2" in the ".cell.c2" "css_element"
    And I should see "1" in the ".cell.c3" "css_element"

  Scenario: View student attempts in a lesson containing only content pages
    Given the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title            | content              |
      | Test lesson name | content   | First page name  | First page contents  |
      | Test lesson name | content   | Second page name | Second page contents |
    And the following "mod_lesson > answers" exist:
      | page                  | answer        | jumpto        |
      | First page name       | Next page     | Next page     |
      | Second page name      | Previous page | Previous page |
      | Second page name      | End of lesson | End of lesson |
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "First page contents"
    And I press "Next page"
    And I should see "Second page contents"
    And I press "End of lesson"
    Then I am on the "Course 1" course page logged in as teacher1
    And I navigate to course participants
    And I follow "Student 1"
    And I follow "Complete report"
    And I should see "Completed"
    And I should see "2" in the ".cell.c1" "css_element"
    And I should see "0" in the ".cell.c2" "css_element"
    And I should see "0" in the ".cell.c3" "css_element"
