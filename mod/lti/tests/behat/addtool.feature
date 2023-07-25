@mod @mod_lti
Feature: Add tools
  In order to provide activities for learners
  As a teacher
  I need to be able to add instances of external tools to a course

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
    # A site tool configured to show as a preconfigured tool and in the activity chooser.
    And the following "mod_lti > tool types" exist:
      | name            | baseurl                                   | coursevisible | state |
      | Teaching Tool 1 | /mod/lti/tests/fixtures/tool_provider.php | 2             | 1     |
    # A course tool in course 1.
    And the following "mod_lti > course tools" exist:
      | name          | baseurl                                   | course |
      | Course tool 1 | /mod/lti/tests/fixtures/tool_provider.php | C1     |

  @javascript
  Scenario: Add a site tool via the activity picker
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Teaching Tool 1" to section "1"
    # For tool that does not support Content-Item message type, the Select content button must be disabled.
    And I set the field "Activity name" to "Test tool activity 1"
    And I expand all fieldsets
    And I set the field "Launch container" to "Embed"
    And the "Select content" "button" should be disabled
    And I press "Save and return to course"
    And I open "Test tool activity 1" actions menu
    And I choose "Edit settings" in the open action menu
    Then the field "Preconfigured tool" matches value "Teaching Tool 1"
    And the "Select content" "button" should be disabled
    And the "Tool URL" "field" should be disabled

  @javascript
  Scenario: Add a course tool via the activity picker
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    When I add a "Course tool 1" to section "1"
    # For tool that does not support Content-Item message type, the Select content button must be disabled.
    And I set the field "Activity name" to "Test tool activity 2"
    And I expand all fieldsets
    And I set the field "Launch container" to "Embed"
    And the "Select content" "button" should be disabled
    And I press "Save and return to course"
    And I open "Test tool activity 2" actions menu
    And I choose "Edit settings" in the open action menu
    Then the field "Preconfigured tool" matches value "Course tool 1"
    And the "Select content" "button" should be disabled
    And the "Tool URL" "field" should be disabled
