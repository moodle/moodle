@mod @mod_choice
Feature: Test the display of the choice module on my home
  In order to know my status in a choice activity
  As a user
  I need to see it in My dashboard.

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
    And I add a "Choice" to section "1"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Choice name | Test choice name |
      | Description | Test choice description |
      | timeopen[enabled] | 1 |
      | timeclose[enabled] | 1 |
      | timeclose[day] | 1 |
      | timeclose[month] | January |
      | timeclose[year] | 2030 |
      | timeclose[hour] | 08 |
      | timeclose[minute] | 00 |
      | Allow choice to be updated | No |
      | option[0] | Option 1 |
      | option[1] | Option 2 |
    And I press "Save and return to course"
    And I log out

  Scenario: View my home as a student before answering the choice
    Given I log in as "student1"
    Then I should see "You have Choices that need attention"
    And I should see "Not answered yet"
    And I log out

  Scenario: View my home as a student after answering the choice
    Given I log in as "student1"
    And I follow "Course 1"
    And I choose "Option 1" from "Test choice name" choice activity
    And I should see "Your selection: Option 1"
    And I should see "Your choice has been saved"
    And "Save my choice" "button" should not exist
    When I follow "Dashboard"
    Then I should not see "You have Choices that need attention"
    And I log out

  Scenario: View my home as a teacher
    Given I log in as "student1"
    And I follow "Course 1"
    And I choose "Option 1" from "Test choice name" choice activity
    And I should see "Your selection: Option 1"
    And I should see "Your choice has been saved"
    And "Save my choice" "button" should not exist
    And I log out
    When I log in as "teacher1"
    Then I should see "You have Choices that need attention"
    And I should see "View 1 responses"
    And I log out
