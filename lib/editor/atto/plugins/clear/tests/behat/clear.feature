@editor @editor_atto @atto @atto_clear @_bug_phantomjs
Feature: Atto clear button
  To format text in Atto, I need to remove formatting

  @javascript
  Scenario: Clear formatting
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "Pisa"
    And I select the text in the "Description" Atto editor
    And I click on "Italic" "button"
    And I click on "Show more buttons" "button"
    And I select the text in the "Description" Atto editor
    When I click on "Clear formatting" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should not see "<i>Pisa"

