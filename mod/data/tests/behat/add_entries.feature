@mod @mod_data
Feature: Users can add entries to database activities
  In order to populate databases
  As a user
  I need to add entries to databases

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name               | intro | course | idnumber |
      | data     | Test database name | n     | C1     | data1    |

  @javascript
  Scenario: Students can add entries to a database
    Given the following "mod_data > fields" exist:
      | database | type | name              | description              |
      | data1    | text | Test field name   | Test field description   |
      | data1    | text | Test field 2 name | Test field 2 description |
    When I am on the "Course 1" course page logged in as student1
    And I add an entry to "Test database name" database with:
      | Test field name | Student original entry |
      | Test field 2 name | Student original entry 2 |
    And I press "Save"
    Then I should see "Student original entry"
    And I open the action menu in "#defaulttemplate-single" "css_element"
    And I choose "Edit" in the open action menu
    And I set the following fields to these values:
      | Test field name | Student original entry |
      | Test field 2 name |  |
    And I press "Save"
    Then I should not see "Student original entry 2"
    And I open the action menu in "#defaulttemplate-single" "css_element"
    And I choose "Edit" in the open action menu
    And I set the following fields to these values:
      | Test field name | Student edited entry |
    And I press "Save"
    And I should see "Student edited entry"
    And I add an entry to "Test database name" database with:
      | Test field name | Student second entry |
    And I press "Save and add another"
    And the field "Test field name" does not match value "Student second entry"
    And I add an entry to "Test database name" database with:
      | Test field name | Student third entry |
    And I press "Save"
    And I select "List view" from the "jump" singleselect
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

  @javascript @editor @editor_atto @atto @atto_h5p
  Scenario: If a new text area entry is added, the filepicker is displayed in the H5P Atto button
    Given I am on the "Course 1" course page logged in as teacher1
    And I add a "Text area" field to "Test database name" database and I fill the form with:
      | Field name | Textarea field name |
    And I am on "Course 1" course homepage
    When I add an entry to "Test database name" database with:
      | Textarea field name | This is the content |
    And I click on "Insert H5P" "button"
    Then I should see "Browse repositories..."

  @javascript
  Scenario: If maximum number of entries is set other than None then add entries should be seen only if number of entries is less than it
    Given I am on the "Test database name" "data activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Maximum number of entries | 2 |
    And I press "Save and display"
    And I add a "Short text" field to "Test database name" database and I fill the form with:
      | Field name | Test1 |
    And I log out
    And I am on the "Test database name" "data activity" page logged in as student1
    And I press "Add entry"
    And I set the field "Test1" to "foo"
    And I press "Save"
    And I press "Add entry"
    And I set the field "Test1" to "bar"
    And I press "Save"
    And I should not see "Add entry"
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
    Given I am on the "Course 1" "enrolment methods" page logged in as teacher1
    And I click on "Enable" "link" in the "Guest access" "table_row"
    And I am on "Course 1" course homepage
    And I add a "Text area" field to "Test database name" database and I fill the form with:
      | Field name | Textarea field name |
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
