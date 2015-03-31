@editor @editor_atto @atto @atto_underline @_bug_phantomjs
Feature: Atto underline button
  To format text in Atto, I need to use the underline button.

  @javascript
  Scenario: Underline some text
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Description" to "Deprecated HTML Tag"
    And I set the field "Text editor" to "Plain text area"
    And I select the text in the "Description" Atto editor
    And I click on "Show more buttons" "button"
    When I click on "Underline" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should see "<u>Deprecated HTML Tag</u>"

