@editor @editor_atto @atto @atto_underline @_bug_phantomjs
Feature: Atto underline button
  To format text in Atto, I need to use the underline button.

  @javascript
  Scenario: Underline some text
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "Deprecated HTML Tag"
    And I select the text in the "Description" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Underline" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then I should see "<u>Deprecated HTML Tag</u>"

