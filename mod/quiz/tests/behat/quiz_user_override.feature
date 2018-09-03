@mod @mod_quiz @javascript
Feature: Quiz user override
  In order to grant a student special access to a quiz
  As a teacher
  I need to create an override for that user.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "activities" exist:
      | activity   | name   | intro              | course | idnumber |
      | quiz       | Quiz 1 | Quiz 1 description | C1     | quiz1    |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And quiz "Quiz 1" contains the following questions:
      | question | page | maxmark |
      | TF1      | 1    |         |
      | TF2      | 1    | 3.0     |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  Scenario: Add, modify then delete a user override
    When I follow "Quiz 1"
    And I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user        | Student1 |
      | id_timeclose_enabled | 1        |
      | timeclose[day]       | 1        |
      | timeclose[month]     | January  |
      | timeclose[year]      | 2020     |
      | timeclose[hour]      | 08       |
      | timeclose[minute]    | 00       |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    Then I click on "Edit" "link" in the "Student One" "table_row"
    And I set the following fields to these values:
      | timeclose[year] | 2030 |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I click on "Delete" "link"
    And I press "Continue"
    And I should not see "Student One"

  Scenario: Being able to modify a user override when the quiz is not available to the student
    Given I follow "Quiz 1"
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the field "Availability" to "Hide from students"
    And I click on "Save and display" "button"
    When I navigate to "User overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user    | Student1 |
      | Attempts allowed | 1        |
    And I press "Save"
    Then "Edit" "icon" should exist in the "Student One" "table_row"
