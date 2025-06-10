@core @core_form
Feature: Read-only forms should work
  In order to use certain forms on large Moodle installations
  As a user
  Relevant featuers of non-editable forms should still work

  @javascript
  Scenario: Shortforms expand collapsing should work for read-only forms - one-section form
    Given I log in as "admin"
    And I visit "/lib/tests/fixtures/readonlyform.php?sections=1"
    When I press "First section"
    Then "Name" "field" should be visible
    And the field "Name" matches value "Important information"
    And I press "First section"
    And "Name" "field" should not be visible

  @javascript
  Scenario: Shortforms expand collapsing should work for read-only forms - two-section form
    Given I log in as "admin"
    And I visit "/lib/tests/fixtures/readonlyform.php?sections=2"
    When I press "Expand all"
    Then "Name" "field" should be visible
    And the field "Name" matches value "Important information"
    And "Other" "field" should be visible
    And the field "Other" matches value "Other information"
    And I press "Collapse all"
    And "Name" "field" should not be visible
    And "Other" "field" should not be visible
