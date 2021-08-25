@mod @mod_lti @core_backup @javascript
Feature: Restoring Moodle 2 backup restores LTI configuration

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
      | Course 2 | C2 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |

  Scenario: Backup and restore course with preconfigured site LTI tool on the same site
    When I log in as "admin"
    And I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And I follow "Manage preconfigured tools"
    And I follow "Add preconfigured tool"
    And I set the following fields to these values:
      | Tool name | My site tool |
      | Tool URL | https://www.moodle.org |
      | lti_coursevisible | 1 |
    And I press "Save changes"
    And I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And "This tool has not yet been used" "text" should exist in the "//div[contains(@id,'tool-card-container') and contains(., 'My site tool')]" "xpath_element"
    And I am on site homepage
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "External tool" to section "1" and I fill the form with:
        | Activity name | My LTI module |
        | Preconfigured tool | My site tool |
        | Launch container | Embed |
    And I should see "My LTI module"
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
    And I am on site homepage
    And I follow "Course 1 copy 1"
    And I open "My LTI module" actions menu
    And I choose "Edit settings" in the open action menu
    Then the field "Preconfigured tool" matches value "My site tool"
    And I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And "This tool is being used 2 times" "text" should exist in the "//div[contains(@id,'tool-card-container') and contains(., 'My site tool')]" "xpath_element"

  @javascript @_switch_window
  Scenario: Backup and restore course with preconfigured course LTI tool on the same site
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    # In the first course create an LTI module that uses a course preconfigured toolю
    And I add a "External tool" to section "1"
    And I set the following fields to these values:
      | Activity name | Test tool activity 2 |
    And I follow "Add preconfigured tool"
    And I switch to "add_tool" window
    And I set the field "Tool name" to "My course tool"
    And I set the field "Tool URL" to "http://www.example.com/lti/provider.php"
    And I set the field "Consumer key" to "my key"
    And I set the field "Shared secret" to "my secret"
    And I set the field "Default launch container" to "Existing window"
    And I press "Save changes"
    And I switch to the main window
    And I press "Save and return to course"
    # Backup course and restore into another course
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into "Course 2" course using this options:
    And I am on site homepage
    And I follow "Course 2"
    # Make sure the copy of the preconfigured tool was created in the second course with both encrtypted and non-encrypted properties.
    And I open "Test tool activity 2" actions menu
    And I choose "Edit settings" in the open action menu
    Then the field "Preconfigured tool" matches value "My course tool"
    And I follow "Edit preconfigured tool"
    And I switch to "edit_tool" window
    Then the field "Tool URL" matches value "http://www.example.com/lti/provider.php"
    And the field "Consumer key" matches value "my key"
    And the field "Shared secret" matches value "my secret"
    And the field "Default launch container" matches value "Existing window"
    And I press "Cancel"
    And I switch to the main window
