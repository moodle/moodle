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

  @javascript
  Scenario: Permissions can be configured to control access to no auto-link
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "roles" exist:
      | name           | shortname | description         | archetype      |
      | Custom teacher | custom1   | Limited permissions | editingteacher |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | custom1        |
    And the following "activity" exists:
      | activity | assign          |
      | course   | C1              |
      | name     | Test assignment |
    And the following "permission overrides" exist:
      | capability          | permission | role    | contextlevel | reference |
      | tiny/noautolink:use | Prohibit   | custom1 | Course       | C1        |
    # Check plugin access as a role with prohibited permissions.
    And I log in as "teacher2"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    When I click on the "Format" menu item for the "Activity instructions" TinyMCE editor
    Then I should not see "No auto-link"
    # Check plugin access as a role with allowed permissions.
    And I log in as "teacher1"
    And I am on the "Test assignment" Activity page
    And I navigate to "Settings" in current page administration
    And I click on the "Format" menu item for the "Activity instructions" TinyMCE editor
    And I should see "No auto-link"
