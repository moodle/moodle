@tool_behat
Feature: Verify that the inplace editable field works as expected
  In order to use behat step definitions
  As a test write
  I need to ensure that the inplace editable works in forms

  Background:
    Given the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
    And the following "activities" exist:
      | activity | course | name                | idnumber |
      | forum    | C1     | My first forum      | forum1   |
      | assign   | C1     | My first assignment | assign1  |
      | quiz     | C1     | My first quiz       | quiz1    |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Using an inplace editable updates the name of an activity
    When I set the field "Edit title" in the "My first assignment" "activity" to "Coursework submission"
    Then I should see "Coursework submission"
    And I should not see "My first assignment"
    But I should see "My first forum"
    And I should see "My first quiz"
    And I set the field "Edit title" in the "Coursework submission" "activity" to "My first assignment"
    And I should not see "Coursework submission"
    But I should see "My first assignment"
    And I should see "My first forum"
    And I should see "My first quiz"
