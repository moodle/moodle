@mod @mod_data
Feature: Users can add entries to database activities
  In order to populate databases
  As a user
  I need to add entries to databases

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |
    And the following "activities" exist:
      | activity | name               | intro | course | idnumber |
      | data     | Test database name | n     | C1     | data1    |

  @javascript
  Scenario: Students can add entries to a database
    Given the following "mod_data > fields" exist:
      | database | type | name              | description              |
      | data1    | text | Test field name   | Test field description   |
      | data1    | text | Test field 2 name | Test field 2 description |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | user     | Test field name        | Test field 2 name        |
      | data1    | student1 | Student original entry | Student original entry 2 |
    And I am on the "data1" Activity page logged in as student1
    And I open the action menu in "#data-listview-content" "css_element"
    And I choose "Edit" in the open action menu
    And I set the following fields to these values:
      | Test field name   | Student original entry |
      | Test field 2 name |                        |
    And I press "Save"
    Then I should not see "Student original entry 2"
    And I open the action menu in "#data-singleview-content" "css_element"
    And I choose "Edit" in the open action menu
    And I set the following fields to these values:
      | Test field name | Student edited entry |
    And I press "Save"
    And I should see "Student edited entry"
    And the following "mod_data > entries" exist:
      | database | user     | Test field name        | Test field 2 name |
      | data1    | student1 | Student second entry   |                   |
      | data1    | student1 | Student third entry    |                   |
    And I am on the "data1" Activity page logged in as student1
    And I should see "Student edited entry"
    And I should see "Student second entry"
    And I should see "Student third entry"
    # Will delete the first one.
    And I open the action menu in ".defaulttemplate-listentry" "css_element"
    And I choose "Delete" in the open action menu
    And I press "Delete"
    And I should not see "Student edited entry"
    And I should see "Student second entry"
    And I should see "Student third entry"

  @javascript
  Scenario: If a new text area entry is added, the filepicker is displayed in the H5P editor dialogue
    Given the following "mod_data > fields" exist:
      | database | type     | name                |
      | data1    | textarea | Textarea field name |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And I am on the "Course 1" course page logged in as teacher1
    When I click on "Test database name" "link"
    And I click on "Add entry" "button"
    And I click on "Insert H5P content" "button"
    Then I should see "Browse repositories..." in the "Insert H5P content" "dialogue"

  @javascript
  Scenario: If maximum number of entries is set other than None then add entries should be seen only if number of entries is less than it
    Given the following "mod_data > fields" exist:
      | database | type | name  |
      | data1    | text | Test1 |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | user     | Test1 |
      | data1    | student1 | foo   |
      | data1    | student1 | bar   |
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Maximum number of entries | 2 |
    And I press "Save and display"
    And I log out
    When I am on the "Test database name" "data activity" page logged in as student1
    Then I should not see "Add entry"
    And I log out
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Maximum number of entries | 3 |
    And I press "Save and display"
    And I log out
    And I am on the "Test database name" "data activity" page logged in as student1
    And I should see "Add entry"

  @javascript
  Scenario: Guest user cannot add entries to a database
    Given the following "mod_data > fields" exist:
      | database | type | name                |
      | data1    | text | Textarea field name |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And I am on the "Course 1" "enrolment methods" page logged in as teacher1
    And I click on "Enable" "link" in the "Guest access" "table_row"
    And I log out
    When I am on the "Test database name" "data activity" page logged in as "guest"
    Then I should not see "Add entry"

  @javascript
  Scenario Outline: Users see the Add entry button in the view page when some field has been created only.
    Given I am on the "Test database name" "data activity" page logged in as <user>
    And I should not see "Add entry"
    And I log out
    When the following "mod_data > fields" exist:
      | database | type | name              | description              |
      | data1    | text | Test field name   | Test field description   |
    Then I am on the "Test database name" "data activity" page logged in as <user>
    And I should see "Add entry"

    Examples:
      | user     |
      | teacher1 |
      | student1 |
