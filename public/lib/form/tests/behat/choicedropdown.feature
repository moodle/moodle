@core
Feature: Choice dropdown form behat test
  In order to use choicelist in quickforms
  As an admin
  I need to be able to test it via behat

  Background:
    Given I log in as "admin"
    And I am on fixture page "/lib/form/tests/behat/fixtures/field_choicedropdown_testpage.php"

  Scenario: Set some value into choice dropdown
    When I set the field "Basic choice dropdown" to "Text option 2"
    And I click on "Send form" "button"
    Then I should see "example0: option2" in the "submitted_data" "region"

  @javascript
  Scenario: Set some value into choice dropdown with javascript enabled
    When I set the field "Basic choice dropdown" to "Text option 2"
    And I click on "Send form" "button"
    Then I should see "example0: option2" in the "submitted_data" "region"

  @javascript
  Scenario: Disable choice dropdown via javascript
    When I click on "Check to disable the first choice dropdown field." "checkbox"
    Then the "Disable if example" "field" should be disabled

  @javascript
  Scenario: Hide choice dropdown via javascript
    Given I should see "Hide if example"
    When I click on "Check to hide the first choice dropdown field." "checkbox"
    Then I should not see "Hide if example"

  @javascript
  Scenario: Use a choice dropdown to disable and hide other fields
    Given I should not see "Hide if element"
    And the "Disabled if element" "field" should be disabled
    When I set the field "Control choice dropdown" to "Show or enable subelements"
    Then I should see "Hide if element"
    And the "Disabled if element" "field" should be enabled
    And I set the field "Control choice dropdown" to "Hide or disable subelements"
    And I should not see "Hide if element"
    And the "Disabled if element" "field" should be disabled
