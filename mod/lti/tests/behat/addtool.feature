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
    And I navigate to "Manage tools" node in "Site administration > Plugins > Activity modules > External tool"
    And I follow "Manage preconfigured tools"
    And I follow "Add preconfigured tool"
    And I set the following fields to these values:
      | Tool name | Teaching Tool 1 |
      | Show when creating activities | Show in activity chooser and as preconfigured tool  |
    And I set the field "Tool base URL/cartridge URL" to local url "/mod/lti/tests/fixtures/tool_provider.html"
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Add a tool via the acitivity picker
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Teaching Tool 1" to section "1" and I fill the form with:
      | Activity name | Test tool activity 1 |
      | Launch container | Embed |
    And I open "Test tool activity 1" actions menu
    And I follow "Edit settings" in the open menu
    Then the field "Preconfigured tool" matches value "Teaching Tool 1"
