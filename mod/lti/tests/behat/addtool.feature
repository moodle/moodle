@mod @mod_lti
Feature: Add tools
  In order to provide activities for learners
  As a teacher
  I need to be able to add external tools to a course

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
    And I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And I follow "Manage preconfigured tools"
    And I follow "Add preconfigured tool"
    And I set the following fields to these values:
      | Tool name | Teaching Tool 1 |
      | Tool configuration usage | Show in activity chooser and as a preconfigured tool |
    And I set the field "Tool URL" to local url "/mod/lti/tests/fixtures/tool_provider.php"
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Add a tool via the activity picker
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Teaching Tool 1" to section "1"
    # For tool that does not support Content-Item message type, the Select content button must be disabled.
    And I set the field "Activity name" to "Test tool activity 1"
    And I expand all fieldsets
    And I set the field "Launch container" to "Embed"
    And the "Select content" "button" should be disabled
    And I press "Save and return to course"
    When I open "Test tool activity 1" actions menu
    And I choose "Edit settings" in the open action menu
    Then the field "Preconfigured tool" matches value "Teaching Tool 1"
    And the "Select content" "button" should be disabled
    And the "Tool URL" "field" should be disabled
