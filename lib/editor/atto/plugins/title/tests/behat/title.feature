@editor @editor_atto @atto @atto_title
Feature: Atto title
  To format text in Atto, I need to add headings

  @javascript
  Scenario: Create a heading
    Given I log in as "admin"
    And I open my profile in edit mode
    And I set the field "Description" to "How The Rock Has Made the WWE World Heavyweight Title More Important Than Ever"
    And I select the text in the "Description" Atto editor
    When I click on "Paragraph styles" "button"
    When I click on "Heading (large)" "link"
    And I press "Update profile"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    Then I should see "<h3>How The Rock"
