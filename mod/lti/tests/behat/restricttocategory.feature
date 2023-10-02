@mod @mod_lti
Feature: Make an LTI only available to specific course categories
  In order to restrict which courses a tool can be used in
  As an administrator
  I need to be able to select which course category the tool is available in

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "categories" exist:
      | name  | category | idnumber |
      | cata  | 0        | cata     |
      | catca | cata     | catca    |
      | catb  | 0        | catb     |
      | catcb | catb     | catcb    |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | cata  |
      | Course 2 | C2 | catb  |
      | Course 3 | C3 | catca |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |
      | teacher1 | C3 | editingteacher |
    And the following "mod_lti > tool types" exist:
      | name            | description        | baseurl                                   | coursevisible | state | lti_coursecategories |
      | Teaching Tool 1 | Tool 1 description | /mod/lti/tests/fixtures/tool_provider.php | 1             | 1     | catb                 |
      | Teaching Tool 2 | Tool 2 description | /mod/lti/tests/fixtures/tool_provider.php | 2             | 1     | catca                |

  Scenario: Tool is set to "Show as preconfigured tool when adding an external tool" on parent category
    Given I am on the "Course 2" course page logged in as teacher1
    When I navigate to "LTI External tools" in current page administration
    Then I should see "Teaching Tool 1" in the "reportbuilder-table" "table"
    And I should not see "Teaching Tool 2" in the "reportbuilder-table" "table"

  @javascript
  Scenario: Tool is set to "Show in activity chooser and as preconfigured tool" on child category
    Given I log in as "teacher1"
    When I am on "Course 3" course homepage with editing mode on
    And I open the activity chooser
    Then I should see "Teaching Tool 2" in the "Add an activity or resource" "dialogue"
    And I should not see "Teaching Tool 1" in the "Add an activity or resource" "dialogue"

  @javascript
  Scenario: View a course in a category in which no tools are available
    Given I log in as "teacher1"
    When I am on "Course 1" course homepage with editing mode on
    And I open the activity chooser
    Then I should not see "Teaching Tool 1" in the "Add an activity or resource" "dialogue"
    And I should not see "Teaching Tool 2" in the "Add an activity or resource" "dialogue"

  @javascript
  Scenario: Editing and saving selected parent / child categories
    Given I log in as "admin"
    And I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And I follow "Manage preconfigured tools"
    And I follow "Add preconfigured tool"
    And I expand all fieldsets
    And I click on "catb" "link"
    And I set the following fields to these values:
      | Tool name | Teaching Tool 3 |
      | Tool configuration usage | Do not show; use only when a matching tool URL is entered |
      | catb | 1 |
    # If parent is selected, child should be selected.
    And the field "catcb" matches value "1"
    # If parent is unselected, child should be unselected.
    And I set the following fields to these values:
    | catb | 0 |
    And the field "catcb" matches value "0"
    # If parent is selected, child is unselected, parent should still be selected.
    # Step 1 - Select parent first so child is selected.
    And I set the following fields to these values:
    | catb  | 1 |
    And the field "catcb" matches value "1"
    # Step 2 - Unselect child but parent should stay as selected.
    And I set the following fields to these values:
    | catcb | 0 |
    And the field "catb" matches value "1"
    And I set the field "Tool URL" to local url "/mod/lti/tests/fixtures/tool_provider.php"
    And I press "Save changes"
    And I wait until the page is ready
    And I should see "Teaching Tool 3"
    When I click on "Update" "link" in the "Teaching Tool 3" "table_row"
    And I expand all fieldsets
    Then the following fields match these values:
      | catb  | 1 |
      | catcb | 0 |

  @javascript
  Scenario: Category restriction only shown for a site tool
    Given the following "mod_lti > tool types" exist:
      | name            | baseurl                                   | coursevisible | state |
      | Teaching Tool 1 | /mod/lti/tests/fixtures/tool_provider.php | 2             | 1     |
    And the following "mod_lti > course tools" exist:
      | name          | description         | baseurl                  | course |
      | Course Tool 1 | Example description | https://example.com/tool | C1     |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "LTI External tools" in current page administration
    When I click on "Add tool" "link"
    And I should not see "Restrict to category"
    And I press "Cancel"
    And I open the action menu in "Course Tool 1" "table_row"
    And I choose "Edit" in the open action menu
    And I should not see "Restrict to category"
    And I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And I follow "Manage preconfigured tools"
    And I follow "Add preconfigured tool"
    And I should see "Restrict to category"
    And I press "Cancel"
    And I click on "Update" "link" in the "Teaching Tool 1" "table_row"
    Then I should see "Restrict to category"
