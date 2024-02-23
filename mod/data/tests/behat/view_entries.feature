@mod @mod_data
Feature: Users can view and search database entries
  In order to find the database entries that I am looking for
  As a user
  I need to list and search the database entries

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Bob       | 1        | student1@example.com |
      | student2 | Alice     | 2        | student2@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "tags" exist:
      | name | isstandard |
      | Tag1 | 1          |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "activities" exist:
      | activity | name               | intro          | course | idnumber |
      | data     | Test database name | Database intro | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type | name              | description              |
      | data1    | text | Test field name   | Test field description   |
      | data1    | text | Test field 2 name | Test field 2 description |
      | data1    | url  | Test field 3 name | Test field 3 description |

  @javascript
  Scenario: Students can view, list and search entries
    Given the following "mod_data > entries" exist:
      | database | Test field name | Test field 2 name | Test field 3 name      |
      | data1    | Student entry 1 |                   | https://moodledev.io   |
      | data1    | Student entry 2 |                   |                        |
      | data1    | Student entry 3 |                   |                        |
    When I log in as "student1"
    And I am on the "Test database name" "data activity" page
    Then I should see "Student entry 1"
    # Confirm that the URL field is displayed as a link.
    And "https://moodledev.io" "link" should exist
    And I should see "Student entry 2"
    And I should see "Student entry 3"
    And I select "Single view" from the "jump" singleselect
    And I should see "Student entry 1"
    # Confirm that the URL field is displayed as a link.
    And "https://moodledev.io" "link" should exist
    And I should not see "Student entry 2"
    And "2" "link" should exist
    And "3" "link" should exist
    And I follow "Next"
    And I should see "Student entry 2"
    And I should not see "Student entry 1"
    And I click on "3" "link" in the "region-main" "region"
    And I should see "Student entry 3"
    And I should not see "Student entry 2"
    And I follow "Previous"
    And I should see "Student entry 2"
    And I should not see "Student entry 1"
    And I should not see "Student entry 3"
    And I select "List view" from the "jump" singleselect
    And I click on "Advanced search" "checkbox"
    And I set the field "Test field name" to "Student entry 1"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And I should see "Student entry 1"
    And I should not see "Student entry 2"
    And I should not see "Student entry 3"
    And I set the field "Test field name" to "Student entry"
    And I set the field "Order" to "Descending"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And "Student entry 3" "text" should appear before "Student entry 2" "text"
    And "Student entry 2" "text" should appear before "Student entry 1" "text"

  @javascript
  Scenario: Check that searching by tags works as expected
    Given the following "mod_data > entries" exist:
      | database | user     | Test field name                 | Test field 2 name                 | Test field 3 name |
      | data1    | student1 | Student original entry untagged | Student original entry untagged 2 |                   |
    And I am on the "Test database name" "data activity" page logged in as student1
    And I click on "Add entry" "button"
    # This is required for now to prevent the tag suggestion menu from overlapping over the Save & view button.
    And I change window size to "large"
    And I set the following fields to these values:
      | Test field name   | Student original entry tagged   |
      | Test field 2 name | Student original entry tagged 2 |
    And I set the field with xpath "//div[@class='datatagcontrol']//input[@type='text']" to "Tag1"
    And I press "Save"
    And I should see "Student original entry"
    And I should see "Tag1" in the "div.tag_list" "css_element"
    And I open the action menu in "#defaulttemplate-single" "css_element"
    And I choose "Edit" in the open action menu
    And I should see "Tag1" in the ".form-autocomplete-selection" "css_element"
    And I follow "Cancel"
    And I select "List view" from the "jump" singleselect
    And I should see "Tag1" in the "div.tag_list" "css_element"
    And I click on "Advanced search" "checkbox"
    And I set the field with xpath "//div[@class='datatagcontrol']//input[@type='text']" to "Tag1"
    And I click on "#page-content" "css_element"
    When I click on "Save settings" "button" in the "data_adv_form" "region"
    Then I should see "Student original entry tagged"
    And I should see "Student original entry tagged 2"
    And I should not see "Student original entry untagged"
    And I should not see "Student original entry untagged 2"

  @javascript
  Scenario: Check that searching by first and last name works as expected
    Given the following "mod_data > entries" exist:
      | database | user     | Test field name | Test field 2 name | Test field 3 name |
      | data1    | student1 | Student entry 1 |                   |                   |
      | data1    | student2 | Student entry 2 |                   |                   |
    When I am on the "Test database name" "data activity" page logged in as teacher1
    And I click on "Advanced search" "checkbox"
    And I set the field "First name" to "Bob"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    Then I should see "Found 1 out of 2 records."
    And I should see "Student entry 1"
    And I should not see "Student entry 2"
    And I set the field "First name" to ""
    And I set the field "Last name" to "2"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And I should see "Found 1 out of 2 records."
    And I should not see "Student entry 1"
    And I should see "Student entry 2"
    # Search: no records found.
    But I set the field "Last name" to ""
    And I set the field "Test field name" to "Student entry 0"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And I should see "No records found."
    And I should not see "Student entry 1"
    And I should not see "Student entry 2"
    # Search all the entries.
    And I set the field "Test field name" to "Student entry"
    And I click on "Save settings" "button" in the "data_adv_form" "region"
    And I should not see "Found 2 out of 2 records."
    And I should see "Student entry 1"
    And I should see "Student entry 2"

  @javascript
  Scenario: Database entries can be deleted in batch if delcheck is present
    Given the following "mod_data > entries" exist:
      | database | user     | Test field name | Test field 2 name      | Test field 3 name |
      | data1    | student1 | Student entry 1 | Some student content 1 | http://moodle.com |
      | data1    | teacher1 | Teacher entry 2 | Some teacher content 2 | http://moodle.com |
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I navigate to "Templates" in current page administration
    And I set the field "Templates tertiary navigation" to "List view template"
    And I set the following fields to these values:
      | Repeated entry | ##delcheck##[[Test field name]]! |
    And I click on "Save" "button" in the "sticky-footer" "region"
    When I navigate to "Database" in current page administration
    When I click on "Select all" "button"
    And I click on "Delete selected" "button"
    And I press "Delete"
    And I should see "No entries yet"

  @javascript
  Scenario: Database entries cannot be deleted in batch if delcheck is not present
    Given the following "mod_data > entries" exist:
      | database | user     | Test field name | Test field 2 name      | Test field 3 name |
      | data1    | student1 | Student entry 1 | Some student content 1 | http://moodle.com |
      | data1    | teacher1 | Teacher entry 2 | Some teacher content 2 | http://moodle.com |
    And I am on the "Test database name" "data activity" page logged in as teacher1
    Then I should not see "Select all"
    And I should not see "Delete selected"

  Scenario Outline: Entries are linked based on autolink and open in new window settings
    # Param1 refers to `Autolink`, param3 refers to `Open in new window`.
    Given the following "mod_data > fields" exist:
      | database | type | name              | param1   | param3   |
      | data1    | url  | URL field name    | <param1> | <param3> |
    And the following "mod_data > entries" exist:
      | database | user     | Test field name  | Test field 2 name  | Test field 3 name   | URL field name |
      | data1    | teacher1 | Test field entry | Test field 2 entry | http://example.com/ | www.moodle.org |
    When I am on the "Test database name" "data activity" page logged in as teacher1
    Then "www.moodle.org" "link" <autolink> exist
    # Verify that the URL field is rendered as a link with the correct href attribute and target set to _blank.
    And "a[target='_blank'][href='http://www.moodle.org']" "css_element" <autolink> exist

    Examples:
      | param1 | param3 | autolink   |
      | 0      | 0      | should not |
      | 1      | 1      | should     |
