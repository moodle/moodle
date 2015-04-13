@editor @editor_atto @atto @atto_unorderedlist @_bug_phantomjs
Feature: Atto unordered list button
  To format text in Atto, I need to use the unordered list button.

  @javascript
  Scenario: Make a list from some text
    Given I log in as "admin"
    And I follow "My profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "Things, dogs, clogs, they're awesome<br/> Rocks, clocks, and socks, they're awesome<br/> Figs, and wigs, and twigs, that's awesome<br/> Everything you see or think or say is awesome"
    And I select the text in the "Description" Atto editor
    When I click on "Unordered list" "button"
    And I press "Update profile"
    And I follow "My preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then I should see "<ul><li>Things, dogs, clogs"

