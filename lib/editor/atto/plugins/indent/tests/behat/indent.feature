@editor @editor_atto @atto @atto_indent
Feature: Indent text in Atto
  To write rich text - I need to indent and outdent things.

  @javascript
  Scenario: Indent
    Given I log in as "admin"
    When I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Description" to "<p>I need some space.</p>"
    And I set the field "Text editor" to "Plain text area"
    And I select the text in the "Description" Atto editor
    And I click on "Show more buttons" "button"
    And I click on "Indent" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should see "class=\"editor-indent\""

  @javascript
  Scenario: Indent and outdent
    Given I log in as "admin"
    When I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Description" to "<p>I need some space.</p>"
    And I set the field "Text editor" to "Plain text area"
    And I select the text in the "Description" Atto editor
    And I click on "Show more buttons" "button"
    And I click on "Indent" "button"
    And I click on "Outdent" "button"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "class=\"editor-indent\""
