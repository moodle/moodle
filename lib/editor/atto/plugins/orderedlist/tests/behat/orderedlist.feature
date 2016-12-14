@editor @editor_atto @atto @atto_orderedlist @_bug_phantomjs
Feature: Atto ordered list button
  To format text in Atto, I need to use the ordered list button.

  @javascript
  Scenario: Make a list from some text
    Given I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Edit profile"
    And I set the field "Description" to "Have you heard the news everyone's talking<br/> Life is good 'cause everything's awesome<br/>"
    And I select the text in the "Description" Atto editor
    When I click on "Ordered list" "button"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I follow "Edit profile"
    Then "//textarea[@id='id_description_editor'][starts-with(text(), '<ol><li>') and contains(normalize-space(.), 'Have you heard the news everyone')]" "xpath_element" should exist
