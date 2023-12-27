@editor @editor_tiny @tiny_noautolink
Feature: Tiny noautolink
  To avoid auto-linking, users need to wrap the URL with the 'nolink' class.

  @javascript
  Scenario: Insert a link, add and remove the auto-link prevention
    Given I log in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > General settings" in site administration
    And I click on "Enable Tiny no auto-link" "link"
    When I open my profile in edit mode
    And I set the field "Description" to "<p>https://moodle.org</p>"

    # Add auto-link prevention.
    And I select the "p" element in position "0" of the "Description" TinyMCE editor
    And I click on the "No auto-link" button for the "Description" TinyMCE editor
    Then the field "Description" matches value "<p><span class='nolink'>https://moodle.org</span></p>"

    # Remove auto-link prevention.
    And I select the "span" element in position "0" of the "Description" TinyMCE editor
    And I click on the "No auto-link" button for the "Description" TinyMCE editor
    Then the field "Description" matches value "<p>https://moodle.org</p>"
