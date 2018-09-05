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
      | activity | name             | intro                   | course | idnumber |
      | lesson   | Test lesson name | Test lesson description | C1     | lesson1  |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Test lesson name"
    And I follow "Add a question page"
    And I set the field "Select a question type" to "True/false"
    And I press "Add a question page"
    And I set the following fields to these values:
      | Page title           | True/false question 1 |
      | Page contents        | Cat is an amphibian |
      | id_answer_editor_0   | False |
      | id_response_editor_0 | Correct |
      | id_jumpto_0          | Next page |
      | id_answer_editor_1   | True |
      | id_response_editor_1 | Wrong |
      | id_jumpto_1          | This page |
    And I press "Save page"

  Scenario: Use course reset to clear all attempt data
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I should see "Cat is an amphibian"
    And I set the following fields to these values:
      | False | 1 |
    And I press "Submit"
    And I press "Continue"
    And I should see "Congratulations - end of lesson reached"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I navigate to "Reports > Overview" in current page administration
    And I should see "Sam1 Student1"
    And I navigate to "Reset" node in "Course administration"
    And I set the following fields to these values:
        | Delete all lesson attempts | 1  |
    And I press "Reset course"
    And I press "Continue"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I navigate to "Reports > Overview" in current page administration
    Then I should see "No attempts have been made on this lesson"

  @javascript
  Scenario: Use course reset to remove user overrides.
    When I follow "Test lesson name"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
        | Override user    | Student1  |
        | Re-takes allowed | 1 |
    And I press "Save"
    And I should see "Sam1 Student1"
    And I navigate to "Reset" node in "Course administration"
    And I set the following fields to these values:
        | Delete all user overrides | 1  |
    And I press "Reset course"
    And I press "Continue"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I navigate to "User overrides" in current page administration
    Then I should not see "Sam1 Student1"

  Scenario: Use course reset to remove group overrides.
    When I follow "Test lesson name"
    And I navigate to "Group overrides" in current page administration
    And I press "Add group override"
    And I set the following fields to these values:
        | Override group   | Group 1  |
        | Re-takes allowed | 1 |
    And I press "Save"
    And I should see "Group 1"
    And I navigate to "Reset" node in "Course administration"
    And I set the following fields to these values:
        | Delete all group overrides | 1  |
    And I press "Reset course"
    And I press "Continue"
    And I am on "Course 1" course homepage
    And I follow "Test lesson name"
    And I navigate to "Group overrides" in current page administration
    Then I should not see "Group 1"
