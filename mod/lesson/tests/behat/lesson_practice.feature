@mod @mod_lesson
Feature: Practice mode in a lesson activity
  In order to improve my students understanding of a subject
  As a teacher
  I need to be able to set ungraded practice lesson activites

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
    And the following "activity" exists:
      | activity                      | lesson             |
      | course                        | C1                 |
      | idnumber                      | 0001               |
      | name                          | Test lesson name   |
    And the following "mod_lesson > page" exist:
      | lesson           | qtype     | title         | content                   |
      | Test lesson name | truefalse | True or False | Paper is made from trees. |
    And the following "mod_lesson > answers" exist:
      | page          | answer | jumpto        | score |
      | True or False | True   | Next page     | 1     |
      | True or False | False  | This page     | 0     |
    And I am on the "Test lesson name" "lesson activity editing" page logged in as teacher1

  Scenario: Non-practice lesson records grades in the gradebook
    Given I set the following fields to these values:
      | Name | Non-practice lesson |
      | Description | This lesson will affect your course grade |
      | Practice lesson | No |
    And I press "Save and display"
    When I am on the "Non-practice lesson" "lesson activity" page logged in as student1
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    Then I should see "View grades"
    And I follow "Grades" in the user menu
    And I am on "Course 1" course homepage
    And I should see "Non-practice lesson"

  Scenario: Practice lesson doesn't record grades in the gradebook
    Given I set the following fields to these values:
      | Name | Practice lesson |
      | Description | This lesson will NOT affect your course grade |
      | Practice lesson | Yes |
    And I press "Save and display"
    When I am on the "Practice lesson" "lesson activity" page logged in as student1
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    Then I should not see "View grades"
    And I follow "Grades" in the user menu
    And I click on "Course 1" "link" in the "Course 1" "table_row"
    And I should not see "Practice lesson"

  Scenario: Practice lesson with scale doesn't record grades in the gradebook
    Given I set the following fields to these values:
      | Name | Practice lesson with scale |
      | Description | This lesson will NOT affect your course grade |
      | Practice lesson | Yes |
      | Type | Scale |
    And I press "Save and display"
    When I am on the "Practice lesson with scale" "lesson activity" page logged in as student1
    And I set the following fields to these values:
      | True | 1 |
    And I press "Submit"
    Then I should not see "View grades"
    And I follow "Grades" in the user menu
    And I click on "Course 1" "link" in the "Course 1" "table_row"
    And I should not see "Practice lesson with scale"
