@mod @mod_data
Feature: Users can add the ##actionsmenu## replacement to the database templates
  In order to display all the actions for entries in templates
  As a teacher
  I need to edit the templates and add the actionsmenu replacement

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name               | intro          | course | idnumber |
      | data     | Test database name | Database intro | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type | name   | description              |
      | data1    | text | field1 | Test field description   |
      | data1    | text | field2 | Test field 2 description |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | user         | field1           | field2                 |
      | data1    | student1     | Student entry 1  | Some student content 1 |
      | data1    | teacher1     | Teacher entry 1  | Some teacher content 1 |
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I navigate to "Templates" in current page administration
    And I set the field "Templates tertiary navigation" to "List view template"
    And I set the following fields to these values:
      | Header         | <table>                                              |
      | Repeated entry | <tr><td>[[field1]]</td><td>##actionsmenu##</td><tr>  |
      | Footer         | </table>                                             |
    And I click on "Save" "button" in the "sticky-footer" "region"
    And I set the field "Templates tertiary navigation" to "Single view template"
    And I set the following fields to these values:
      | Single view template | <table><tr><td>[[field1]]</td><td>[[field2]]</td><td>##actionsmenu##</td><tr></table> |
    And I click on "Save" "button" in the "sticky-footer" "region"

  @javascript
  Scenario: The ##actionsmenu## replacement displays the expected actions with default settings depending on the user permissions
    Given I navigate to "Database" in current page administration
    # Teachers should be able to edit/delete all the entries.
    When I open the action menu in "Student entry 1" "table_row"
    Then I should see "Show more"
    And I should see "Edit"
    And I should see "Delete"
    But I should not see "Approve"
    And I should not see "Undo approval"
    And I should not see "Export to portfolio"
    And I press the escape key
    And I open the action menu in "Teacher entry 1" "table_row"
    And I should see "Show more"
    And I should see "Edit"
    And I should see "Delete"
    # Single view (for teacher).
    And I choose "Show more" in the open action menu
    And I should see "Teacher entry 1"
    And I should see "Some teacher content 1"
    And I should not see "Student entry 1"
    And I open the action menu in "Teacher entry 1" "table_row"
    And I should not see "Show more"
    And I should see "Edit"
    And I should see "Delete"
    And I should not see "Approve"
    And I should not see "Undo approval"
    And I should not see "Export to portfolio"
    And I press the escape key
    And I follow "Previous page"
    And I should see "Student entry 1"
    And I should see "Some student content 1"
    And I should not see "Teacher entry 1"
    And I open the action menu in "Student entry 1" "table_row"
    And I should not see "Show more"
    And I should see "Edit"
    And I should see "Delete"
    And I log out
    # Students only should edit/delete their entries.
    But I am on the "Test database name" "data activity" page logged in as student1
    And I open the action menu in "Student entry 1" "table_row"
    And I should see "Show more"
    And I should see "Edit"
    And I should see "Delete"
    And I should not see "Approve"
    And I should not see "Undo approval"
    And I should not see "Export to portfolio"
    And I press the escape key
    And I open the action menu in "Teacher entry 1" "table_row"
    And I should see "Show more"
    And I should not see "Edit"
    And I should not see "Delete"
    # Single view (for student).
    And I choose "Show more" in the open action menu
    And I should see "Teacher entry 1"
    And I should see "Some teacher content 1"
    And I should not see "Student entry 1"
    And I should not see "Actions" in the "Teacher entry 1" "table_row"
    And I follow "Previous page"
    And I should see "Student entry 1"
    And I should see "Some student content 1"
    And I should not see "Teacher entry 1"
    And I open the action menu in "Student entry 1" "table_row"
    And I should not see "Show more"
    And I should see "Edit"
    And I should see "Delete"
    And I should not see "Approve"
    And I should not see "Undo approval"
    And I should not see "Export to portfolio"

  @javascript
  Scenario: The ##actionsmenu## replacement displays the Approval/Undo approval options
    Given I navigate to "Settings" in current page administration
    And I follow "Entries"
    And I set the field "Approval required" to "Yes"
    And I press "Save and display"
    When I navigate to "Database" in current page administration
    # Teachers should be able to approve/unapprove all the entries from list view.
    And I open the action menu in "Student entry 1" "table_row"
    Then I should see "Approve"
    And I should not see "Undo approval"
    And I choose "Approve" in the open action menu
    And I should see "Entry approved"
    And I press "Dismiss this notification"
    And I open the action menu in "Student entry 1" "table_row"
    And I should see "Undo approval"
    And I should not see "Approve" in the ".menu-action-text" "css_element"
    And I press the escape key
    And I open the action menu in "Teacher entry 1" "table_row"
    And I should see "Undo approval"
    And I should not see "Approve" in the ".menu-action-text" "css_element"
    # Single view (for teacher).
    And I choose "Show more" in the open action menu
    And I should see "Teacher entry 1"
    And I open the action menu in "Teacher entry 1" "table_row"
    And I should see "Undo approval"
    And I should not see "Approve"
    And I press the escape key
    And I follow "Previous page"
    And I should see "Student entry 1"
    And I open the action menu in "Student entry 1" "table_row"
    And I should not see "Approve"
    And I should see "Undo approval"
    # Check entries can be approved/unapproved from single view too.
    And I choose "Undo approval" in the open action menu
    And I should see "Entry unapproved"
    And I press "Dismiss this notification"
    And I open the action menu in "Student entry 1" "table_row"
    And I should see "Approve"
    And I should not see "Undo approval"
    And I log out
    # Students should not see the Approve/Undo approval options.
    But I am on the "Test database name" "data activity" page logged in as student1
    And I open the action menu in "Teacher entry 1" "table_row"
    And I should not see "Approve"
    And I should not see "Undo approval"
    And I press the escape key
    And I open the action menu in "Student entry 1" "table_row"
    And I should not see "Approve"
    And I should not see "Undo approval"
    # Single view (for student).
    And I choose "Show more" in the open action menu
    And I should see "Student entry 1"
    And I open the action menu in "Student entry 1" "table_row"
    And I should not see "Approve"
    And I should not see "Undo approval"
    And I follow "Next page"
    And I should see "Teacher entry 1"
    And I should not see "Actions" in the "Teacher entry 1" "table_row"

  @javascript
  Scenario: The ##actionsmenu## replacement displays the Export to portfolio options
    Given I log in as "admin"
    And the following config values are set as admin:
      | enableportfolios | 1 |
    And I navigate to "Plugins > Portfolios > Manage portfolios" in site administration
    And I set portfolio instance "File download" to "Enabled and visible"
    And I click on "Save" "button"
    And I log out
    And I am on the "Test database name" "data activity" page logged in as teacher1
    # Teachers should be able to export to portfolio all the entries from list view.
    When I open the action menu in "Student entry 1" "table_row"
    Then I should see "Export to portfolio"
    And I choose "Export to portfolio" in the open action menu
    And I should see "Configure exported data"
    And I press "Cancel"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I open the action menu in "Teacher entry 1" "table_row"
    And I should see "Export to portfolio"
    # Single view (for teacher).
    And I choose "Show more" in the open action menu
    And I should see "Teacher entry 1"
    And I open the action menu in "Teacher entry 1" "table_row"
    And I should see "Export to portfolio"
    And I press the escape key
    And I follow "Previous page"
    And I should see "Student entry 1"
    And I open the action menu in "Student entry 1" "table_row"
    And I should see "Export to portfolio"
    # Check entries can be exported from single view too.
    And I choose "Export to portfolio" in the open action menu
    And I should see "Configure exported data"
    And I log out
    # Students should only export their entries.
    But I am on the "Test database name" "data activity" page logged in as student1
    And I open the action menu in "Teacher entry 1" "table_row"
    And I should not see "Export to portfolio"
    And I press the escape key
    And I open the action menu in "Student entry 1" "table_row"
    And I should see "Export to portfolio"
    And I choose "Export to portfolio" in the open action menu
    And I should see "Configure exported data"
    And I press "Cancel"
    And I click on "Yes" "button" in the "Confirm" "dialogue"
    And I open the action menu in "Teacher entry 1" "table_row"
    # Single view (for student).
    And I choose "Show more" in the open action menu
    And I should see "Teacher entry 1"
    And I should not see "Actions" in the "Teacher entry 1" "table_row"
    And I follow "Previous page"
    And I should see "Student entry 1"
    And I open the action menu in "Student entry 1" "table_row"
    And I should see "Export to portfolio"
    And I choose "Export to portfolio" in the open action menu
    And I should see "Configure exported data"

  @javascript
  Scenario: The ##actionsmenu## replacement does not display the Export to portfolio option when there are no portfolios enabled
    Given I log in as "admin"
    And the following config values are set as admin:
      | enableportfolios | 1 |
    And I log out
    And I am on the "Test database name" "data activity" page logged in as teacher1
    When I open the action menu in "Student entry 1" "table_row"
    Then I should not see "Export to portfolio"
    And I log out
    # If we enable, at least, one portfolio, the Export to portfolio option should be displayed.
    But I log in as "admin"
    And I navigate to "Plugins > Portfolios > Manage portfolios" in site administration
    And I set portfolio instance "File download" to "Enabled and visible"
    And I click on "Save" "button"
    And I log out
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I open the action menu in "Student entry 1" "table_row"
    And I should see "Export to portfolio"

  @javascript
  Scenario: The Edit option in the ##actionsmenu## replacement is working
    Given I navigate to "Database" in current page administration
    # Teachers should be able to edit any entry.
    And I open the action menu in "Student entry 1" "table_row"
    When I choose "Edit" in the open action menu
    And I set the field "field2" to "Some MODIFIED BY THE TEACHER student content 1"
    And I click on "Save" "button"
    # Single view (for teacher).
    Then I should see "Some MODIFIED BY THE TEACHER student content 1"
    And I should not see "Some student content 1"
    And I open the action menu in "Student entry 1" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "field2" to "Some MORE TEACHER MODIFICATIONS FOR student content 1"
    And I click on "Save" "button"
    And I should see "Some MORE TEACHER MODIFICATIONS FOR student content 1"
    And I should not see "Some MODIFIED BY THE TEACHER student content 1"
    And I log out
    # Students only should edit their entries.
    But I am on the "Test database name" "data activity" page logged in as student1
    And I open the action menu in "Student entry 1" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "field2" to "Some MODIFIED student content 1"
    And I click on "Save" "button"
    # Single view (for student).
    And I should see "Some MODIFIED student content 1"
    And I should not see "Some MORE TEACHER MODIFICATIONS FOR student content 1"
    And I open the action menu in "Student entry 1" "table_row"
    And I choose "Edit" in the open action menu
    And I set the field "field2" to "Some MORE MODIFICATIONS FOR student content 1"
    And I click on "Save" "button"
    And I should see "Some MORE MODIFICATIONS FOR student content 1"
    And I should not see "Some MODIFIED student content 1"

  @javascript
  Scenario: The Delete option in the ##actionsmenu## replacement is working
    Given the following "mod_data > entries" exist:
      | database | user         | field1           | field2                 |
      | data1    | student1     | Student entry 2  | Some student content 2 |
      | data1    | teacher1     | Teacher entry 2  | Some teacher content 2 |
    And I navigate to "Database" in current page administration
    # Teachers should be able to delete any entry.
    And I open the action menu in "Student entry 1" "table_row"
    When I choose "Delete" in the open action menu
    Then I should see "Delete entry"
    # Cancel doesn't delete the entry.
    And I click on "Cancel" "button" in the "Confirm" "dialogue"
    And I open the action menu in "Teacher entry 1" "table_row"
    # But Delete removes the entry.
    And I choose "Delete" in the open action menu
    And I should see "Delete entry"
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    And I should see "Entry deleted"
    And I should not see "Teacher entry 1"
    And I should see "Teacher entry 2"
    And I should see "Student entry 1"
    And I should see "Student entry 2"
    # Single view (for teacher).
    And I open the action menu in "Teacher entry 2" "table_row"
    And I choose "Delete" in the open action menu
    And I should see "Delete entry"
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    And I should see "Entry deleted"
    And I should not see "Teacher entry 1"
    And I should not see "Teacher entry 2"
    And I should see "Student entry 1"
    And I should see "Student entry 2"
    And I log out
    # Students only should edit their entries.
    But I am on the "Test database name" "data activity" page logged in as student1
    And I open the action menu in "Student entry 1" "table_row"
    When I choose "Delete" in the open action menu
    Then I should see "Delete entry"
    # Cancel doesn't delete the entry.
    And I click on "Cancel" "button" in the "Confirm" "dialogue"
    And I open the action menu in "Student entry 1" "table_row"
    # But Delete removes the entry.
    And I choose "Delete" in the open action menu
    And I should see "Delete entry"
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    And I should see "Entry deleted"
    And I should not see "Teacher entry 1"
    And I should not see "Teacher entry 2"
    And I should not see "Student entry 1"
    And I should see "Student entry 2"
    # Single view (for student).
    And I open the action menu in "Student entry 2" "table_row"
    And I choose "Delete" in the open action menu
    And I should see "Delete entry"
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    And I should see "Entry deleted"
    And I should not see "Teacher entry 1"
    And I should not see "Teacher entry 2"
    And I should not see "Student entry 1"
    And I should not see "Student entry 2"
