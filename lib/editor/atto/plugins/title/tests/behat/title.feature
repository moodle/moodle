@atto @atto_title
Feature: Atto title
  To format text in Atto, I need to add headings

  @javascript
  Scenario: Create a heading
    Given I log in as "admin"
    And I follow "Admin User"
    And I follow "Edit profile"
    And I set the field "Text editor" to "Plain text area"
    And I set the field "Description" to "How The Rock Has Made the WWE World Heavyweight Title More Important Than Ever"
    And I select the text in the "Description" field
    When I click on "Paragraph styles" "button"
    When I click on "Heading 1" "link"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should see "<h3>How The Rock"

