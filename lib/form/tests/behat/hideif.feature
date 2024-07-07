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

  Scenario: When 'eq' hideIf conditions are not met, the relevant elements are shown
    When I add an assign activity to course "Course 1" section "1"
    And I expand all fieldsets
    And I set the field "Students submit in groups" to "Yes"
    Then I should see "Require group to make submission"
    And I should see "Require all group members submit"
    And I should see "Grouping for student groups"

  Scenario: When 'eq' hideIf conditions are met, the relevant elements are hidden
    When I add a assign activity to course "Course 1" section "1"
    And I expand all fieldsets
    And I set the field "Students submit in groups" to "No"
    Then I should not see "Require group to make submission"
    And I should not see "Require all group members to submit"
    And I should not see "Grouping for student groups"

  Scenario: The editor is hidden when 'eq' hideIf conditions are met
    Given I am on fixture page "/lib/form/tests/behat/fixtures/editor_hideif_disabledif_form.php"
    And I should see "My test editor"
    When I click on "Hide" "radio"
    Then I should not see "My test editor"

  Scenario: The static element is hidden when 'eq' hideIf conditions are met
    Given I am on fixture page "/lib/form/tests/behat/fixtures/static_hideif_disabledif_form.php"
    And I should see "Static with form elements"
    When I click on "Hide" "radio"
    Then I should not see "Static with form elements"
    And I click on "Enable" "radio"
    And I should see "Static with form elements"
