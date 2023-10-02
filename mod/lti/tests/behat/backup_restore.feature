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
    Given the following "mod_lti > tool types" exist:
      | name         | description           | baseurl                | coursevisible | state |
      | My site tool | Site tool description | https://www.moodle.org | 2             | 1     |
    And the following "mod_lti > tool instances" exist:
      | name          | tool         | course |
      | My LTI module | My site tool | C1     |
    And I am on the "Course 1" course page logged in as admin
    And I should see "My LTI module"
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
    And I am on site homepage
    And I follow "Course 1 copy 1"
    Then I should see "My LTI module"
    And I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And "This tool is being used 2 times" "text" should exist in the "//div[contains(@id,'tool-card-container') and contains(., 'My site tool')]" "xpath_element"

  @javascript
  Scenario: Backup and restore course with preconfigured course LTI tool on the same site
    Given the following "mod_lti > course tools" exist:
      | name           | description         | baseurl                                 | course | lti_resourcekey | lti_password | lti_launchcontainer |
      | My course tool | Example description | http://www.example.com/lti/provider.php | C1     | my key          | my secret    | 5                   |
    # In the first course create an LTI module that uses a course preconfigured tool
    And the following "mod_lti > tool instances" exist:
      | name                 | tool           | course |
      | Test tool activity 2 | My course tool | C1     |
    And I am on the "Course 1" course page logged in as admin
    # Backup course and restore into another course
    And I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    When I restore "test_backup.mbz" backup into "Course 2" course using this options:
    # Make sure the copy of the preconfigured tool was created in the second course with both encrypted and non-encrypted properties.
    And I am on "Course 2" course homepage with editing mode on
    And I open "Test tool activity 2" actions menu
    And I choose "Edit settings" in the open action menu
    And the field "Activity name" matches value "Test tool activity 2"
    And I am on "Course 1" course homepage
    And I navigate to "LTI External tools" in current page administration
    Then I should see "My course tool"
    And I open the action menu in "My course tool" "table_row"
    And I choose "Edit" in the open action menu
    And the field "Tool URL" matches value "http://www.example.com/lti/provider.php"
    And the field "Consumer key" matches value "my key"
    And the field "Shared secret" matches value "my secret"
    And the field "Default launch container" matches value "Existing window"
