@editor @editor_tiny @tiny_noautolink
Feature: Tiny noautolink
    In order to prevent auto-linking in TinyMCE
    As a User
    I need be able to apply the auto-link prevention feature to the selected text

  Background:
    Given I log in as "admin"
    And I navigate to "Plugins > Text editors > TinyMCE editor > General settings" in site administration
    And I toggle the "Enable No auto-link" admin switch "on"
    When I open my profile in edit mode
    And I set the field "Description" to "<p>https://moodle.org</p>"

  @javascript
  Scenario: Add and remove auto-link prevention to URLs
    Given I open my profile in edit mode
    And I set the field "Description" to "<p>https://moodle.org</p>"
    # Add auto-link prevention.
    And I select the "p" element in position "0" of the "Description" TinyMCE editor
    When I click on the "No auto-link" button for the "Description" TinyMCE editor
    Then the field "Description" matches value "<p><span class='nolink'>https://moodle.org</span></p>"
    # Remove auto-link prevention.
    And I select the "span" element in position "0" of the "Description" TinyMCE editor
    And I click on the "No auto-link" button for the "Description" TinyMCE editor
    And the field "Description" matches value "<p>https://moodle.org</p>"

  @javascript
  Scenario: Add and remove auto-link prevention to simple text
    Given I open my profile in edit mode
    And I set the field "Description" to "Some text"
    # Add auto-link prevention.
    And I select the "p" element in position "0" of the "Description" TinyMCE editor
    When I click on the "No auto-link" button for the "Description" TinyMCE editor
    Then the field "Description" matches value "<p><span class='nolink'>Some text</span></p>"
    # Remove auto-link prevention.
    And I select the "span" element in position "0" of the "Description" TinyMCE editor
    And I click on the "No auto-link" button for the "Description" TinyMCE editor
    And the field "Description" matches value "<p>Some text</p>"
