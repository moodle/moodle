@core @javascript @core_form
Feature: hideIf functionality in forms
  For forms including hideIf functions
  As a user
  If I trigger the hideIf condition then the form elements will be hidden

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I turn editing mode on

  Scenario: When 'eq' hideIf conditions are not met, the relevant elements are shown
    When I add a "Assignment" to section "1"
    And I expand all fieldsets
    And I set the field "Students submit in groups" to "Yes"
    Then I should see "Require group to make submission"
    And I should see "Require all group members submit"
    And I should see "Grouping for student groups"

  Scenario: When 'eq' hideIf conditions are met, the relevant elements are hidden
    When I add a "Assignment" to section "1"
    And I expand all fieldsets
    And I set the field "Students submit in groups" to "No"
    Then I should not see "Require group to make submission"
    And I should not see "Require all group members to submit"
    And I should not see "Grouping for student groups"
