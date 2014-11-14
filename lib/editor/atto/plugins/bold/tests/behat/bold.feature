@editor @editor_atto @atto @atto_bold @_bug_phantomjs
Feature: Atto bold button
  To format text in Atto, I need to use the bold button.

  @javascript
  Scenario: Bold some text
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Text editor" to "Plain text area"
    And I set the field "Description" to "Badger"
    And I select the text in the "Description" Atto editor
    When I click on "Bold" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should see "<b>Badger</b>"

  @javascript
  Scenario: Unbold some text
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Text editor" to "Plain text area"
    And I set the field "Description" to "Mouse"
    And I select the text in the "Description" Atto editor
    When I click on "Bold" "button"
    And I click on "Bold" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "<b>Mouse</b>"
    And I should see "Mouse"
