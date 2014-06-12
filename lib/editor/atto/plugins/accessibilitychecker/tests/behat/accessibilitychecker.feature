@editor @editor_atto @atto @atto_accessibilitychecker
Feature: Atto accessibility checker
  To write accessible text in Atto, I need to check for accessibility warnings.

  @javascript
  Scenario: Images with no alt
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Description" to "<p>Some plain text</p><img src='/broken-image'/><p>Some more text</p>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "Images require alternative text."

  @javascript
  Scenario: Low contrast
    Given I log in as "admin"
    And I navigate to "Edit profile" node in "My profile settings"
    And I set the field "Description" to "<p style='color: #7c7cff; background-color: #ffffff;'>Hard to read</p>"
    When I click on "Show more buttons" "button"
    And I click on "Accessibility checker" "button"
    Then I should see "The colours of the foreground and background text do not have enough contrast."
