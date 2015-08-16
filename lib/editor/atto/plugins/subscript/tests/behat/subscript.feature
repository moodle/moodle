@editor @editor_atto @atto @atto_subscript @_bug_phantomjs
Feature: Atto subscript button
  To format text in Atto, I need to use the subscript button.

  @javascript
  Scenario: Subscript some text
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "Submarine"
    And I select the text in the "Description" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Subscript" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then I should see "<sub>Submarine</sub>"

  @javascript
  Scenario: Subscript some text in enclosed in superscript
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "<sup>Submarine</sup>"
    And I select the text in the "Description" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Subscript" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then I should see "<sub>Submarine</sub>"

