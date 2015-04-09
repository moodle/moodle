@editor @editor_atto @atto @atto_charmap
Feature: Atto charmap button
  To format text in Atto, I need to add symbols

  @javascript
  Scenario: Insert symbols
    Given I log in as "admin"
    And I follow "My profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "<p>1980 Mullet</p>"
    And I select the text in the "Description" Atto editor
    When I click on "Show more buttons" "button"
    And I click on "Insert character" "button"
    And I click on "a - macron" "button"
    And I press "Update profile"
    And I follow "My preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then I should see "ā"
