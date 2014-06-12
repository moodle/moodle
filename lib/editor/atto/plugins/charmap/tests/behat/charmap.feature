@editor @editor_atto @atto @atto_charmap
Feature: Atto charmap button
  To format text in Atto, I need to add symbols

  @javascript
  Scenario: Insert symbols
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Text editor" to "Plain text area"
    And I set the field "Description" to "<p>1980 Mullet</p>"
    And I select the text in the "Description" Atto editor
    When I click on "Show more buttons" "button"
    And I click on "Insert character" "button"
    And I click on "copyright sign" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should see "(c)"

