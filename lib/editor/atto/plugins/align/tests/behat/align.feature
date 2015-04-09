@editor @editor_atto @atto @atto_align
Feature: Atto align text
  To format text in Atto, I need to use the align buttons.

  @javascript
  Scenario: Right align some text
    Given I log in as "admin"
    And I follow "My profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "<p>Fascism</p>"
    And I click on "Show more buttons" "button"
    And I select the text in the "Description" Atto editor
    When I click on "Right align" "button"
    And I press "Update profile"
    And I follow "My preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then I should see "style=\"text-align:right;\""

  @javascript
  Scenario: Left align some text
    Given I log in as "admin"
    And I follow "My profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "<p>Communism</p>"
    And I click on "Show more buttons" "button"
    And I select the text in the "Description" Atto editor
    When I click on "Right align" "button"
    And I click on "Left align" "button"
    And I press "Update profile"
    And I follow "My preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then I should see "style=\"text-align:left;\""

  @javascript
  Scenario: Center align some text
    Given I log in as "admin"
    And I follow "My profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "<p>United Future</p>"
    And I click on "Show more buttons" "button"
    And I select the text in the "Description" Atto editor
    When I click on "Center" "button"
    And I press "Update profile"
    And I follow "My preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then I should see "style=\"text-align:center;\""

