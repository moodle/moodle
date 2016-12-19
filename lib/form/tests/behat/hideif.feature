@core @javascript
Feature: hideIf functionality in forms
  For forms including hideIf functions
  As a user
  If I trigger the hideIf condition then the form elements will be hidden

  Background:
    Given the following "activities" exist:
      | activity | name | intro                                                                   | course               | section | idnumber |
      | label    | L1   | <a href="lib/form/tests/fixtures/formhideiftestpage.php">HideIfLink</a> | Acceptance test site | 1       | L1       |
    And I am on site homepage
    And I follow "HideIfLink"

  Scenario: When 'eq' hideIf conditions are not met, the relevant elements are shown
    When I set the field "Select yesno example" to "Yes"
    Then I should see "Test eq hideif"
    And "#id_testeqhideif" "css_element" should be visible

  Scenario: When 'eq' hideIf conditions are met, the relevant elements are hidden
    When I set the field "Select yesno example" to "No"
    Then I should not see "Test eq hideif"
    And "#id_testeqhideif" "css_element" should not be visible

  Scenario: When 'checked' hideIf conditions are not met, the relevant elements are shown
    When I set the field "Checkbox example" to "0"
    Then I should see "Test checked hideif"
    And "#id_testcheckedhideif" "css_element" should be visible

  Scenario: When 'checked' hideIf conditions are met, the relevant elements are hidden
    When I set the field "Checkbox example" to "1"
    Then I should not see "Test checked hideif"
    And "#id_testcheckedhideif" "css_element" should not be visible

  Scenario: When 'notchecked' hideIf conditions are not met, the relevant elements are shown
    When I set the field "Checkbox example" to "1"
    Then I should see "Test not checked hideif"
    And "#id_testnotcheckedhideif" "css_element" should be visible

  Scenario: When 'notchecked' hideIf conditions are met, the relevant elements are hidden
    When I set the field "Checkbox example" to "0"
    Then I should not see "Test not checked hideif"
    And "#id_testnotcheckedhideif" "css_element" should not be visible

  Scenario: When 'in' hideIf conditions are not met, the relevant elements are shown
    When I set the field "Select example" to "3"
    Then I should see "Test in hideif"
    And "#id_testinhideif" "css_element" should be visible

  Scenario: When 'in' hideIf conditions are met, the relevant elements are hidden
    When I set the field "Select example" to "2"
    Then I should not see "Test in hideif"
    And "#id_testinhideif" "css_element" should not be visible
