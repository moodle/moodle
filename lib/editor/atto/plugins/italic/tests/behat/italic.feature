@editor @editor_atto @atto @atto_italic @_bug_phantomjs
Feature: Atto italic button
  To format text in Atto, I need to use the italic button.

  @javascript
  Scenario: Italicise some text
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Description" to "Tower of Pisa"
    And I set the field "Text editor" to "Plain text area"
    And I select the text in the "Description" Atto editor
    When I click on "Italic" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should see "<i>Tower of Pisa</i>"

  @javascript
  Scenario: Toggle italics in some text
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Description" to "GHD - for hair"
    And I set the field "Text editor" to "Plain text area"
    And I select the text in the "Description" Atto editor
    When I click on "Italic" "button"
    And I click on "Italic" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "<i>GHD - for hair</i>"
    And I should see "GHD - for hair"
