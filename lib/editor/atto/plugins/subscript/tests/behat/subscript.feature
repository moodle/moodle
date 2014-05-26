@atto @atto_subscript @_bug_phantomjs
Feature: Atto subscript button
  To format text in Atto, I need to use the subscript button.

  @javascript
  Scenario: Subscript some text
    Given I log in as "admin"
    And I follow "Admin User"
    And I follow "Edit profile"
    And I set the field "Description" to "Submarine"
    And I set the field "Text editor" to "Plain text area"
    And I select the text in the "Description" field
    And I click on "Show more buttons" "button"
    When I click on "Subscript" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should see "<sub>Submarine</sub>"

