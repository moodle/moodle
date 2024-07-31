@editor @editor_atto @atto @atto_link
Feature: Add links to Atto
  To write rich text - I need to add links.

  @javascript
  Scenario: Insert a links
    Given the following "user private file" exists:
      | user     | admin                                          |
      | filepath | lib/editor/atto/tests/fixtures/moodle-logo.png |
    And I log in as "admin"
    When I open my profile in edit mode
    And I set the field "Description" to "Super cool"
    And I select the text in the "Description" Atto editor
    And I click on "Link" "button"
    Then the field "Text to display" matches value "Super cool"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.png" "link"
    And I click on "Select this file" "button"
    And I click on "Update profile" "button"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    And I should see "Super cool</a>"

  @javascript
  Scenario: Insert a link without providing text to display
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on "Link" "button"
    And I set the field "Enter a URL" to "https://moodle.org/"
    Then the field "Text to display" matches value "https://moodle.org/"
    And I click on "Create link" "button"
    And I should see "https://moodle.org/"
    And I click on "Link" "button"
    And the field "Text to display" matches value "https://moodle.org/"
    And the field "Enter a URL" matches value "https://moodle.org/"
    And I click on "Close" "button" in the "Create link" "dialogue"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    And I should see "https://moodle.org/</a>"

  @javascript
  Scenario: Insert a link with providing text to display
    Given I log in as "admin"
    When I open my profile in edit mode
    And I click on "Link" "button"
    And I set the field "Text to display" to "Moodle - Open-source learning platform"
    And I set the field "Enter a URL" to "https://moodle.org/"
    And I click on "Create link" "button"
    Then I should see "Moodle - Open-source learning platform"
    And I click on "Link" "button"
    And the field "Text to display" matches value "Moodle - Open-source learning platform"
    And the field "Enter a URL" matches value "https://moodle.org/"
    And I click on "Close" "button" in the "Create link" "dialogue"
    And I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    And I should see "Moodle - Open-source learning platform</a>"
    And I should not see "https://moodle.org/</a>"

  @javascript
  Scenario: Edit a link that already had a custom text to display
    Given I log in as "admin"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Plain text area"
    And I press "Save changes"
    And I click on "Edit profile" "link" in the "region-main" "region"
    And I set the field "Description" to "<a href=\"https://moodle.org/\">Moodle - Open-source learning platform</a>"
    And I click on "Update profile" "button"
    And I follow "Preferences" in the user menu
    And I follow "Editor preferences"
    And I set the field "Text editor" to "Atto HTML editor"
    And I press "Save changes"
    When I click on "Edit profile" "link" in the "region-main" "region"
    Then I should see "Moodle - Open-source learning platform"
    And I click on "Link" "button"
    And the field "Text to display" matches value "Moodle - Open-source learning platform"
    And the field "Enter a URL" matches value "https://moodle.org/"

  @javascript
  Scenario: Insert a link for an image
    Given the following "user private file" exists:
      | user     | admin                                          |
      | filepath | lib/editor/atto/tests/fixtures/moodle-logo.png |
    And I log in as "admin"
    And I open my profile in edit mode
    And I click on "Insert or edit image" "button"
    And I click on "Browse repositories..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "moodle-logo.png" "link"
    And I click on "Select this file" "button"
    And I set the field "Describe this image for someone who cannot see it" to "It's the Moodle"
    And I press "Save image"
    And I select the text in the "Description" Atto editor
    And I press the right key
    And I press the shift left key
    And I click on "Link" "button"
    And I set the field "Enter a URL" to "https://moodle.org/"
    And I set the field "Text to display" to "Moodle - Open-source learning platform"
    And I click on "Create link" "button"
    When I click on "Show more buttons" "button"
    And I click on "HTML" "button"
    Then I should see "<a href=\"https://moodle.org/\" title=\"Moodle - Open-source learning platform\"><img"
    And I click on "HTML" "button"
    And I select the text in the "Description" Atto editor
    And I press the shift left key
    And I click on "Insert or edit image" "button"
    And the field "Describe this image for someone who cannot see it" matches value "It's the Moodle"
    And I click on "Close" "button" in the "Image properties" "dialogue"
    And I click on "Link" "button"
    And the field "Text to display" matches value "Moodle - Open-source learning platform"
    And the field "Enter a URL" matches value "https://moodle.org/"
