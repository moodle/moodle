@editor @editor_atto @atto @atto_collapse
Feature: Atto collapse button
  To access all the tools in Atto, I need to toggle the toolbar

  @javascript
  Scenario: Toggle toolbar
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link" in the "region-main" "region"
    When I click on "Show more buttons" "button"
    Then "Equation editor" "button" should be visible
    And I click on "Show fewer buttons" "button"
    Then "Equation editor" "button" should not be visible

