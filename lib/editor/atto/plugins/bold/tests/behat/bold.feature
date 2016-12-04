@editor @editor_atto @atto @atto_bold @_bug_phantomjs
Feature: Atto bold button
  To format text in Atto, I need to use the bold button.

  @javascript
  Scenario: Bold some text
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link" in the "region-main" "region"
    And I set the field "Description" to "Badger"
    And I select the text in the "Description" Atto editor
    When I click on "Bold" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should see "<b>Badger</b>"

  @javascript
  Scenario: Unbold some text
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I click on "Edit profile" "link" in the "region-main" "region"
    And I set the field "Description" to "Mouse"
    And I select the text in the "Description" Atto editor
    When I click on "Bold" "button"
    And I click on "Bold" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should not see "<b>Mouse</b>"
    And I should see "Mouse"
