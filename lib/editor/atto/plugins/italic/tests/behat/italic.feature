@editor @editor_atto @atto @atto_italic @_bug_phantomjs
Feature: Atto italic button
  To format text in Atto, I need to use the italic button.

  @javascript
  Scenario: Italicise some text
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "Tower of Pisa"
    And I select the text in the "Description" Atto editor
    When I click on "Italic" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should see "<i>Tower of Pisa</i>"

  @javascript
  Scenario: Toggle italics in some text
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "GHD - for hair"
    And I select the text in the "Description" Atto editor
    When I click on "Italic" "button"
    And I click on "Italic" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should not see "<i>GHD - for hair</i>"
    And I should see "GHD - for hair"
