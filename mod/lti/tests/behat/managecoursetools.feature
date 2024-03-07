@mod @mod_lti
Feature: Manage course tools
  In order to provide richer experiences for learners
  As a teacher
  I need to be able to add external tools to a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "course" exists:
      | fullname    | Course 1 |
      | shortname   | C1       |
      | category    | 0        |
      | format      | topics   |
      | numsections | 1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Create a course tool from the zero state
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "LTI External tools" in current page administration
    And I should see "LTI External tools are add-on apps"
    And I should see "There are no LTI External tools yet."
    When I click on "Add tool" "link"
    And I press "Cancel"
    Then I should see "LTI External tools are add-on apps"
    And I should see "There are no LTI External tools yet."
    And I click on "Add tool" "link"
    And I set the following fields to these values:
      | Tool name        | Teaching Tool 1                 |
      | Tool URL         | http://example.com              |
      | Tool description | A short description of the tool |
    And I press "Save changes"
    And I should see "Teaching Tool 1 added"
    And I should see "A short description of the tool" in the "Teaching Tool 1" "table_row"

  Scenario: Viewing a site level tool in the course tools table
    # The first tool isn't visible in courses, the next two are, and the last tool is in a pending state and is not visible.
    Given the following "mod_lti > tool types" exist:
      | name         | description         | baseurl                   | coursevisible | state |
      | Example tool | Another description | https://example.com/tool1 | 0             | 1     |
      | Test tool 2  | Tool2 description   | https://example.com/tool2 | 1             | 1     |
      | Test tool 3  | Tool3 description   | https://example.com/tool3 | 2             | 1     |
      | Test tool 4  | Tool4 description   | https://example.com/tool4 | 2             | 2     |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to "LTI External tools" in current page administration
    Then I should see "Test tool 2" in the "reportbuilder-table" "table"
    And "You don't have permission to edit this tool" "icon" should exist in the "Test tool 2" "table_row"
    And I should see "Test tool 3" in the "reportbuilder-table" "table"
    And "You don't have permission to edit this tool" "icon" should exist in the "Test tool 3" "table_row"
    And I should not see "Example tool" in the "reportbuilder-table" "table"
    And I should not see "Test tool 4" in the "reportbuilder-table" "table"

  Scenario: Viewing course tools without the capability to add/edit but having the capability to use
    Given the following "role capability" exists:
      | role                             | editingteacher |
      | mod/lti:addcoursetool            | prohibit       |
      | mod/lti:addpreconfiguredinstance | allow          |
    And the following "mod_lti > course tools" exist:
      | name      | description         | baseurl                  | course |
      | Test tool | Example description | https://example.com/tool | C1     |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to "LTI External tools" in current page administration
    Then "You don't have permission to edit this tool" "icon" should exist in the "Test tool" "table_row"

  Scenario: Viewing course tools with the capability to add/edit and without the capability to use
    Given the following "role capability" exists:
      | role                             | editingteacher |
      | mod/lti:addcoursetool            | allow          |
      | mod/lti:addpreconfiguredinstance | prohibit       |
    And the following "mod_lti > course tools" exist:
      | name      | description         | baseurl                  | course |
      | Test tool | Example description | https://example.com/tool | C1     |
    When I am on the "Course 1" course page logged in as teacher1
    Then "LTI External tools" "link" should not exist in current page administration

  @javascript
  Scenario: Edit a course tool
    Given the following "mod_lti > course tools" exist:
      | name      | description         | baseurl                  | course |
      | Test tool | Example description | https://example.com/tool | C1     |
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "LTI External tools" in current page administration
    And the "Edit" item should exist in the "Actions" action menu of the "Test tool" "table_row"
    And the "Delete" item should exist in the "Actions" action menu of the "Test tool" "table_row"
    When I open the action menu in "Test tool" "table_row"
    And I choose "Edit" in the open action menu
    And I press "Cancel"
    Then I should see "Test tool" in the "reportbuilder-table" "table"
    And I open the action menu in "Test tool" "table_row"
    And I choose "Edit" in the open action menu
    And I set the following fields to these values:
      | Tool name        | Test tool (edited)                       |
      | Tool URL         | http://example.com                       |
      | Tool description | A short description of the tool (edited) |
    And I press "Save changes"
    And I should see "Changes saved"
    And I should see "A short description of the tool (edited)" in the "Test tool (edited)" "table_row"

  @javascript
  Scenario: Navigate through the listing of course tools
    Given 20 "mod_lti > course tools" exist with the following data:
    | name        | Test tool [count]                   |
    | description | Example description [count]         |
    | baseurl     | https://www.example.com/tool[count] |
    | course      | C1                                  |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to "LTI External tools" in current page administration
    Then I should see "Test tool 1" in the "reportbuilder-table" "table"
    And I click on "Name" "link"
    And I should see "Test tool 20" in the "reportbuilder-table" "table"
    And I click on "2" "link" in the "page" "region"
    And I should see "Test tool 1" in the "reportbuilder-table" "table"

  @javascript
  Scenario: Delete a course tool
    Given the following "mod_lti > course tools" exist:
      | name         | description         | baseurl                          | course |
      | Test tool    | Example description | https://example.com/tool         | C1     |
      | Another tool | Example 123         | https://another.example.com/tool | C1     |
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "LTI External tools" in current page administration
    When I open the action menu in "Test tool" "table_row"
    And I choose "Delete" in the open action menu
    Then I should see "This will delete Test tool from the available LTI tools in your course."
    And I click on "Cancel" "button" in the "Delete Test tool" "dialogue"
    And I should see "Test tool" in the "reportbuilder-table" "table"
    And I open the action menu in "Test tool" "table_row"
    And I choose "Delete" in the open action menu
    And I should see "This will delete Test tool from the available LTI tools in your course."
    And I click on "Delete" "button" in the "Delete Test tool" "dialogue"
    And I should see "Test tool deleted"
    And I should not see "Test tool" in the "reportbuilder-table" "table"

  @javascript
  Scenario: Add a course tool using a cartridge URL
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "LTI External tools" in current page administration
    When I click on "Add tool" "link"
    And I set the following fields to these values:
      | Tool name        | Test tool 1             |
      | Tool description | Test tool 1 description |
    And I set the field "Tool URL" to local url "/mod/lti/tests/fixtures/ims_cartridge_basic_lti_link.xml"
    And I press "Save changes"
    Then I should see "Test tool 1" in the "reportbuilder-table" "table"
    # The cartridge description, if set, overrides the description set in the type edit form (bug?).
    And I should see "Example tool description" in the "Test tool 1" "table_row"
    And I open the action menu in "Test tool 1" "table_row"
    And I choose "Edit" in the open action menu
    And the field "Tool name" matches value "Test tool 1"
    And the field "Tool URL" matches value "http://www.example.com/lti/provider.php"
    And the field "Icon URL" matches value "http://download.moodle.org/unittest/test.jpg"
    And the field "Secure icon URL" matches value "https://download.moodle.org/unittest/test.jpg"

  @javascript
  Scenario: Site tool appearing in activity chooser according to settings
    Given the following "mod_lti > tool types" exist:
      | name            | baseurl                                   | coursevisible | state |
      | Teaching Tool 1 | /mod/lti/tests/fixtures/tool_provider.php | 2             | 1     |
      | Teaching Tool 2 | /mod/lti/tests/fixtures/tool_provider.php | 1             | 1     |
      | Teaching Tool 3 | /mod/lti/tests/fixtures/tool_provider.php | 0             | 1     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 2 | C2        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C2     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Add an activity or resource" "button" in the "New section" "section"
    And I should see "Teaching Tool 1" in the ".modal-body" "css_element"
    And I should not see "Teaching Tool 2" in the ".modal-body" "css_element"
    And I should not see "Teaching Tool 3" in the ".modal-body" "css_element"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    And I navigate to "LTI External tools" in current page administration
    And I should not see "Teaching Tool 3"
    And I click on "Don't show in activity chooser" "field" in the "Teaching Tool 1" "table_row"
    And I click on "Show in activity chooser" "field" in the "Teaching Tool 2" "table_row"
    And I am on "Course 1" course homepage
    And I click on "Add an activity or resource" "button" in the "New section" "section"
    And I should not see "Teaching Tool 1" in the ".modal-body" "css_element"
    And I should see "Teaching Tool 2" in the ".modal-body" "css_element"
    And I should not see "Teaching Tool 3" in the ".modal-body" "css_element"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"

    # Should not affect other courses.
    And I am on "Course 2" course homepage
    And I click on "Add an activity or resource" "button" in the "New section" "section"
    And I should see "Teaching Tool 1" in the ".modal-body" "css_element"
    And I should not see "Teaching Tool 2" in the ".modal-body" "css_element"
    And I should not see "Teaching Tool 3" in the ".modal-body" "css_element"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"

    And I am on "Course 1" course homepage
    And I navigate to "LTI External tools" in current page administration
    And I click on "Show in activity chooser" "field" in the "Teaching Tool 1" "table_row"
    And I click on "Don't show in activity chooser" "field" in the "Teaching Tool 2" "table_row"
    And I am on "Course 1" course homepage
    And I click on "Add an activity or resource" "button" in the "New section" "section"
    And I should see "Teaching Tool 1" in the ".modal-body" "css_element"
    And I should not see "Teaching Tool 2" in the ".modal-body" "css_element"
    And I should not see "Teaching Tool 3" in the ".modal-body" "css_element"

    When the following "role capability" exists:
      | role                             | editingteacher |
      | mod/lti:addcoursetool            | prohibit       |
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "LTI External tools" in current page administration
    Then the "Don't show in activity chooser" "field" should be disabled
    And the "Show in activity chooser" "field" should be disabled

  @javascript
  Scenario: Course tool appearing in activity chooser according to settings
    Given the following "mod_lti > course tools" exist:
      | name          | baseurl                                   | course | coursevisible |
      | Course Tool 1 | /mod/lti/tests/fixtures/tool_provider.php | C1     | 2             |
      | Course Tool 2 | /mod/lti/tests/fixtures/tool_provider.php | C1     | 1             |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I click on "Add an activity or resource" "button" in the "New section" "section"
    And I should see "Course Tool 1" in the ".modal-body" "css_element"
    And I should not see "Course Tool 2" in the ".modal-body" "css_element"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    And I navigate to "LTI External tools" in current page administration
    And I click on "Don't show in activity chooser" "field" in the "Course Tool 1" "table_row"
    And I click on "Show in activity chooser" "field" in the "Course Tool 2" "table_row"
    And I am on "Course 1" course homepage
    And I click on "Add an activity or resource" "button" in the "New section" "section"
    And I should not see "Course Tool 1" in the ".modal-body" "css_element"
    And I should see "Course Tool 2" in the ".modal-body" "css_element"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    And I navigate to "LTI External tools" in current page administration
    And I click on "Show in activity chooser" "field" in the "Course Tool 1" "table_row"
    And I click on "Don't show in activity chooser" "field" in the "Course Tool 2" "table_row"
    And I am on "Course 1" course homepage
    And I click on "Add an activity or resource" "button" in the "New section" "section"
    And I should see "Course Tool 1" in the ".modal-body" "css_element"
    And I should not see "Course Tool 2" in the ".modal-body" "css_element"

    When the following "role capability" exists:
      | role                             | editingteacher |
      | mod/lti:addcoursetool            | prohibit       |
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "LTI External tools" in current page administration
    Then the "Don't show in activity chooser" "field" should be disabled
    And the "Show in activity chooser" "field" should be disabled

  @javascript
  Scenario: Site and course tools settings are preserved when backup and restore
    Given the following "mod_lti > tool types" exist:
      | name            | baseurl                                   | coursevisible | state |
      | Teaching Tool 1 | /mod/lti/tests/fixtures/tool_provider.php | 2             | 1     |
      | Teaching Tool 2 | /mod/lti/tests/fixtures/tool_provider.php | 1             | 1     |
    And the following "mod_lti > course tools" exist:
      | name          | description         | baseurl                  | course |
      | Course Tool 1 | Example description | https://example.com/tool | C1     |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Teaching Tool 1" to section "1" using the activity chooser
    And I set the field "Activity name" to "Test tool activity 1"
    And I press "Save and return to course"
    And I add a "Course Tool 1" to section "1" using the activity chooser
    And I set the field "Activity name" to "Course tool activity 1"
    And I press "Save and return to course"
    And I navigate to "LTI External tools" in current page administration
    And I click on "Don't show in activity chooser" "field" in the "Teaching Tool 1" "table_row"
    And I click on "Show in activity chooser" "field" in the "Teaching Tool 2" "table_row"
    And I click on "Don't show in activity chooser" "field" in the "Course Tool 1" "table_row"
    And I am on "Course 1" course homepage
    And I add a "Teaching Tool 2" to section "1" using the activity chooser
    And I set the field "Activity name" to "Test tool activity 2"
    And I press "Save and return to course"
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    And I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Restored course |
    And I should see "Restored course"
    And I click on "Add an activity or resource" "button" in the "New section" "section"
    Then I should not see "Teaching Tool 1" in the ".modal-body" "css_element"
    And I should see "Teaching Tool 2" in the ".modal-body" "css_element"
    And I should not see "Course Tool 2" in the ".modal-body" "css_element"
    And I click on "Close" "button" in the ".modal-dialog" "css_element"
    And I navigate to "LTI External tools" in current page administration
    And I should see "Show in activity chooser" in the "Teaching Tool 1" "table_row"
    And I should see "Don't show in activity chooser" in the "Teaching Tool 2" "table_row"
    And I should see "Show in activity chooser" in the "Course Tool 1" "table_row"
