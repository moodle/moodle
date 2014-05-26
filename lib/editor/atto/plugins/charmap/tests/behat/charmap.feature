@atto @atto_charmap
Feature: Atto charmap button
  To format text in Atto, I need to add symbols

  @javascript
  Scenario: Insert synbols
    Given I log in as "admin"
    And I follow "Admin User"
    And I follow "Edit profile"
    And I set the field "Text editor" to "Plain text area"
    And I set the field "Description" to "1980 Mullet"
    When I click on "Show more buttons" "button"
    And I click on "Insert character" "button"
    And I click on "copyright sign" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should see "(c)1980 Mullet"

