@editor @editor_atto @atto @atto_superscript @_bug_phantomjs
Feature: Atto superscript button
  To format text in Atto, I need to use the superscript button.

  @javascript
  Scenario: Subscript some text
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link" in the "region-main" "region"
    And I set the field "Description" to "Helicopter"
    And I select the text in the "Description" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Superscript" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should see "<sup>Helicopter</sup>"

  @javascript
  Scenario: Superscript some text that is enclosed in subscript
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link" in the "region-main" "region"
    And I set the field "Description" to "<sub>Helicopter</sub>"
    And I select the text in the "Description" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Superscript" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should see "<sup>Helicopter</sup>"

