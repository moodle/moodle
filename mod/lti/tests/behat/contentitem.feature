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
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Teaching Tool 1" to section "1"
    Then "Select content" "button" should be visible
    And the "Select content" "button" should be enabled

  Scenario: Editing the settings for an instance of a tool configured with Deep Linking support
    Given the following "mod_lti > tool instances" exist:
    | name                 | tool            | course |
    | Test tool activity 1 | Teaching Tool 1 | C1     |
    When I am on the "Test tool activity 1" "lti activity editing" page logged in as teacher1
    Then I should see "Select content"
    And the "Select content" "button" should be enabled
