@mod @mod_lesson
Feature: Lesson reset
  In order to reuse past lessons
  As a teacher
  I need to remove all previous data.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Tina | Teacher1 | teacher1@example.com |
      | student1 | Sam1 | Student1 | student1@example.com |
      | student2 | Sam2 | Student2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "activities" exist:
      | activity | name             | course | idnumber |
      | lesson   | Test lesson name | C1     | lesson1  |
    And the following "mod_lesson > page" exist:
      | lesson           | qtype     | title                 | content             |
      | Test lesson name | truefalse | True/false question 1 | Cat is an amphibian |
    And the following "mod_lesson > answers" exist:
      | page                  | answer    | response | jumpto    | score |
      | True/false question 1 | False     | Correct  | Next page | 1     |
      | True/false question 1 | True      | Wrong    | This page | 0     |

  Scenario: Use course reset to clear all attempt data
    When I am on the "Test lesson name" "lesson activity" page logged in as student1
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I navigate to "Reports" in current page administration
    And I should see "Sam1 Student1"
    And I am on the "Course 1" "reset" page
    And I set the following fields to these values:
        | All lesson attempts | 1  |
    And I press "Reset course"
    And I press "Continue"
    And I am on the "Test lesson name" "lesson activity" page
    And I navigate to "Reports" in current page administration
    Then I should see "No attempts have been made on this lesson"

  @javascript
  Scenario: Use course reset to remove user overrides
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I follow "Add user override"
    And I set the following fields to these values:
        | Override user    | Student1  |
        | Re-takes allowed | 1 |
    And I press "Save"
    And I should see "Sam1 Student1"
    And I am on the "Course 1" "reset" page
    And I press "Deselect all"
    And I set the following fields to these values:
        | All user overrides | 1  |
    And I press "Reset course"
    And I click on "Reset course" "button" in the "Reset course?" "dialogue"
    And I press "Continue"
    And I am on the "Test lesson name" "lesson activity" page
    And I navigate to "Overrides" in current page administration
    Then I should not see "Sam1 Student1"

  @javascript
  Scenario: Use course reset to remove group overrides
    When I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I follow "Add group override"
    And I set the following fields to these values:
        | Override group   | Group 1  |
        | Re-takes allowed | 1 |
    And I press "Save"
    And I should see "Group 1"
    And I am on the "Course 1" "reset" page
    And I press "Deselect all"
    And I set the following fields to these values:
        | All group overrides | 1  |
    And I press "Reset course"
    And I click on "Reset course" "button" in the "Reset course?" "dialogue"
    And I press "Continue"
    And I am on the "Test lesson name" "lesson activity" page
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    Then I should not see "Group 1"
