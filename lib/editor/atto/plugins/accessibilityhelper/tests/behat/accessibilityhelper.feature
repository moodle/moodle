@editor @editor_atto @atto @atto_accessibilityhelper
Feature: Atto accessibility helper
  To use a screen reader effectively in Atto, I may need additional information about the text

  @javascript
  Scenario: Images and links
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<p>Some plain text</p><img src='/broken-image' alt='Image 1'/><p><a href='#fsd'>Some link text</a></p>"
    And I select the text in the "Description" Atto editor
    When I click on "Show more buttons" "button"
    And I click on "Screenreader helper" "button"
    Then I should see "Links in text editor"
    And I should see "Some link text"
    And I should see "Images in text editor"
    And I should see "Image 1"
    And I should not see "No images"
    And I should not see "No links"

  @javascript
  Scenario: Styles
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "<p>Some plain text</p>"
    When I click on "Show more buttons" "button"
    And I select the text in the "Description" Atto editor
    And I click on "Unordered list" "button"
    And I click on "Screenreader helper" "button"
    And I select the text in the "Description" Atto editor
    # This shows the current HTML tags applied to the selected text.
    # This is required because they are not always read by a screen reader.
    Then I should see "UL, LI"
