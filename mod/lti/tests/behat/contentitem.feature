@mod @mod_lti @mod_lti_contentitem
Feature: Content-Item support
  In order to easily add activities and content in a course from an external tool
  As a teacher
  I need to utilise a tool that supports the Content-Item Message type

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I navigate to "Manage tools" node in "Site administration > Plugins > Activity modules > External tool"
    And I follow "configure a tool manually"
    And I set the field "Tool name" to "Teaching Tool 1"
    And I set the field "Tool base URL/cartridge URL" to local url "/mod/lti/tests/fixtures/tool_provider.php"
    And I set the field "Tool configuration usage" to "Show in activity chooser and as a preconfigured tool"
    And I expand all fieldsets
    And I set the field "Content-Item Message" to "1"
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Tool that supports Content-Item Message type should be able to configure a tool via the Select content button
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Teaching Tool 1" to section "1"
    Then the "Select content" "button" should be enabled

  @javascript
  Scenario: Adding a preconfigured tool that does not support Content-Item.
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Teaching Tool 1" to section "1"
    And the "Select content" "button" should be enabled
    And I set the field "Activity name" to "Test tool activity 1"
    And I expand all fieldsets
    And I set the field "Launch container" to "Embed"
    And I press "Save and return to course"
    And I open "Test tool activity 1" actions menu
    And I follow "Edit settings" in the open menu
    Then the field "Preconfigured tool" matches value "Teaching Tool 1"
    # When editing settings, the Select content button should be disabled.
    And the "Select content" "button" should be disabled

  @javascript
  Scenario: Selecting a preconfigured tool that supports Content-Item
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "External tool" to section "1"
    And the field "Preconfigured tool" matches value "Automatic, based on launch URL"
    And the "Select content" "button" should be disabled
    And I set the field "Activity name" to "Test tool activity 1"
    And I set the field "Preconfigured tool" to "Teaching Tool 1"
    Then the "Select content" "button" should be enabled
    And I set the field "Preconfigured tool" to "Automatic, based on launch URL"
    And the "Select content" "button" should be disabled
