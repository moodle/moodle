@mod @mod_lti
Feature: Content-Item support
  In order to easily add activities and content in a course from an external tool
  As a teacher
  I need to utilise a tool that supports the Deep Linking (Content-Item Message) type

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
    And the following "mod_lti > tool types" exist:
      | name            | description        | baseurl                                   | coursevisible | state | lti_contentitem |
      | Teaching Tool 1 | Tool 1 description | /mod/lti/tests/fixtures/tool_provider.php | 2             | 1     | 1               |

  @javascript
  Scenario: Tool that supports Deep Linking should be able to configure a tool via the Select content button
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Teaching Tool 1" to section "1"
    Then the "Select content" "button" should be enabled

  @javascript
  Scenario: Editing a tool's settings that was configured from a preconfigured tool that supports Deep Linking.
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Teaching Tool 1" to section "1"
    And the "Select content" "button" should be enabled
    And I set the field "Activity name" to "Test tool activity 1"
    And I expand all fieldsets
    And I set the field "Launch container" to "Embed"
    And I press "Save and return to course"
    And I open "Test tool activity 1" actions menu
    And I choose "Edit settings" in the open action menu
    Then the field "Preconfigured tool" matches value "Teaching Tool 1"
    And the "Select content" "button" should be enabled
