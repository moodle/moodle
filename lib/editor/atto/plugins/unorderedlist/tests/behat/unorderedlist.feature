@editor @editor_atto @atto @atto_unorderedlist @_bug_phantomjs
Feature: Atto unordered list button
  To format text in Atto, I need to use the unordered list button.

  @javascript
  Scenario: Make a list from some text
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "Things, dogs, clogs, they're awesome<br/> Rocks, clocks, and socks, they're awesome<br/> Figs, and wigs, and twigs, that's awesome<br/> Everything you see or think or say is awesome"
    And I select the text in the "Description" Atto editor
    When I click on "Unordered list" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then "//textarea[@id='id_description_editor'][starts-with(text(), '<ul><li>') and contains(normalize-space(.), 'Things, dogs, clogs')]" "xpath_element" should exist
