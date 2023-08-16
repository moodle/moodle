@mod @mod_lti
Feature: Manage course tools
  In order to provide richer experiences for learners
  As a teacher
  I need to be able to add external tools to a course

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Create a course tool from the zero state
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "LTI External tools" in current page administration
    And I should see "LTI External tools are add-on apps"
    And I should see "There are no LTI external tools yet"
    When I click on "Add tool" "link"
    And I press "Cancel"
    Then I should see "LTI External tools are add-on apps"
    And I should see "There are no LTI external tools yet"
    And I click on "Add tool" "link"
    And I set the following fields to these values:
      | Tool name        | Teaching Tool 1                 |
      | Tool URL         | http://example.com              |
      | Tool description | A short description of the tool |
    And I press "Save changes"
    And I should see "Teaching Tool 1 added"
    And I should see "A short description of the tool" in the "Teaching Tool 1" "table_row"

  Scenario: Viewing a site level tool in the course tools table
    Given the following "mod_lti > tool types" exist:
      | name         | description         | baseurl                   | coursevisible |
      | Example tool | Another description | https://example.com/tool1 | 0             |
      | Test tool 2  | Tool2 description   | https://example.com/tool2 | 1             |
      | Test tool 3  | Tool3 description   | https://example.com/tool3 | 2             |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to "LTI External tools" in current page administration
    Then I should see "Test tool 2" in the "reportbuilder-table" "table"
    And "You don't have permission to edit this tool" "icon" should exist in the "Test tool 2" "table_row"
    And I should see "Test tool 3" in the "reportbuilder-table" "table"
    And "You don't have permission to edit this tool" "icon" should exist in the "Test tool 3" "table_row"
    And I should not see "Example tool" in the "reportbuilder-table" "table"

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
      | mod/lti:addmanualinstance        | allow          |
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
    And I should see "Test tool removed"
    And I should not see "Test tool" in the "reportbuilder-table" "table"
