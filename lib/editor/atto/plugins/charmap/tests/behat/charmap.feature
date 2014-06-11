@atto @atto_charmap
Feature: Atto charmap button
  To format text in Atto, I need to add symbols

  @javascript
  Scenario: Insert symbols
    Given I log in as "admin"
    And I follow "Admin User"
    And I follow "Edit profile"
    And I set the field "Text editor" to "Plain text area"
    And I set the field "Description" to "<p>1980 Mullet</p>"
    And I select the text in the "Description" field
    When I click on "Show more buttons" "button"
    And I click on "Insert character" "button"
    And I click on "copyright sign" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should see "(c)"

