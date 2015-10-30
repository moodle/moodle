@editor @editor_atto @atto @atto_accessibilitychecker
Feature: Atto accessibility checker
  To write accessible text in Atto, I need to check for accessibility warnings.

  @javascript
  Scenario: Images with no alt
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Description" to "<p>Some plain text</p><img src='/broken-image' width='1' height='1'/><p>Some more text</p>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "Images require alternative text."
    And I follow "/broken-image"
    And I wait "2" seconds
    And I click on "Image" "button"
    And the field "Enter URL" matches value "/broken-image"
    And I set the field "Describe this image" to "No more warning!"
    And I press "Save image"
    And I press "Accessibility checker"
    And I should see "Congratulations, no accessibility problems found!"
    And I click on ".moodle-dialogue-focused .closebutton" "css_element"
    And I select the text in the "Description" Atto editor
    And I click on "Image" "button"
    And I set the field "Describe this image" to ""
    And I set the field "Description not necessary" to "1"
    And I press "Save image"
    And I press "Accessibility checker"
    And I should see "Congratulations, no accessibility problems found!"

  @javascript
  Scenario: Low contrast
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Description" to "<p style='color: #7c7cff; background-color: #ffffff;'>Hard to read</p>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "The colours of the foreground and background text do not have enough contrast."
