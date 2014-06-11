@atto @atto_clear @_bug_phantomjs
Feature: Atto clear button
  To format text in Atto, I need to remove formatting

  @javascript
  Scenario: Clear formatting
    Given I log in as "admin"
    And I follow "Admin User"
    And I follow "Edit profile"
    And I set the field "Text editor" to "Plain text area"
    And I set the field "Description" to "<p><i>Pisa</i></p>"
    When I click on "Show more buttons" "button"
    And I select the text in the "Description" field
    And I click on "Clear formatting" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "<i>Pisa"

