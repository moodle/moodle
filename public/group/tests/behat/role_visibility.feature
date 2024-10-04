@core @core_group
Feature: Test role visibility for the groups management page
  In order to control access
  As an admin
  I need to control which roles can see each other

  Background: Set up some groups
    Given  the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | learner1 | Learner   | 1        | learner1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | manager1 | Manager   | 1        | manager1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | learner1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
      | manager1 | C1     | manager        |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    And the following "group members" exist:
      | user     | group |
      | learner1 | G1    |
      | teacher1 | G1    |
      | manager1 | G1    |

  Scenario: Check the default roles are visible
    Given I log in as "manager1"
    And I am on the "Course 1" "groups" page
    When I set the field "groups" to "Group 1 (3)"
    And I press "Show members for group"
    Then "optgroup[label='No roles']" "css_element" should not exist in the "#members" "css_element"
    And "optgroup[label='Student']" "css_element" should exist in the "#members" "css_element"
    And "optgroup[label='Teacher']" "css_element" should exist in the "#members" "css_element"
    And "optgroup[label='Manager']" "css_element" should exist in the "#members" "css_element"
    And I log out

  Scenario: Do not allow managers to view any roles and check they are hidden
    Given I log in as "teacher1"
    And I am on the "Course 1" "groups" page
    When I set the field "groups" to "Group 1 (3)"
    And I press "Show members for group"
    Then "optgroup[label='No roles']" "css_element" should exist in the "#members" "css_element"
    And "optgroup[label='Student']" "css_element" should exist in the "#members" "css_element"
    And "optgroup[label='Teacher']" "css_element" should exist in the "#members" "css_element"
    And "optgroup[label='Manager']" "css_element" should not exist in the "#members" "css_element"
    And I log out
