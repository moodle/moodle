@mod @mod_lesson
Feature: In Dashboard, teacher can see the number of student attempts to lessons
  In order to know the number of student attempts to a lesson
  As a teacher
  I need to see it in Dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And I log in as "teacher1"

  Scenario: number of student attempts
    Given the following "activity" exists:
      | activity | lesson                  |
      | course   | C1                      |
      | idnumber | 0001                    |
      | name     | Test lesson name        |
      | retake   | 1                       |
    And the following "mod_lesson > pages" exist:
      | lesson           | qtype     | title                 | content                   |
      | Test lesson name | truefalse | True/false question 1 | Cat is an amphibian       |
      | Test lesson name | truefalse | True/false question 2 | Paper is made from trees. |
      | Test lesson name | truefalse | True/false question 3 | 1+1=2                     |
    And the following "mod_lesson > answers" exist:
      | page                  | answer | response | jumpto        | score |
      | True/false question 1 | False  | Correct  | Next page     | 1     |
      | True/false question 1 | True   | Wrong    | This page     | 0     |
      | True/false question 2 | True   | Correct  | Next page     | 1     |
      | True/false question 2 | False  | Wrong    | This page     | 0     |
      | True/false question 3 | True   | Correct  | Next page     | 1     |
      | True/false question 3 | False  | Wrong    | This page     | 0     |
    And I am on the "Test lesson name" "lesson activity editing" page
    And I expand all fieldsets
    And I set the following fields to these values:
      | id_deadline_enabled | 1 |
      | deadline[day] | 1 |
      | deadline[month] | January |
      | deadline[year] | 2030 |
      | deadline[hour] | 08 |
      | deadline[minute] | 00 |
    And I press "Save and display"
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
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
    And I should see "1+1=2"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 1 (out of 3)."
    And I follow "Return to Course 1"
    And I follow "Test lesson name"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "1+1=2"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 3 (out of 3)."
    And I log out
    And I am on the "Test lesson name" "lesson activity" page logged in as student2
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Paper is made from trees."
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "1+1=2"
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I should see "Your score is 2 (out of 3)."
