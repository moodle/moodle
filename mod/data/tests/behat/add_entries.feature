@mod @mod_data
Feature: Users can add entries to database activities
  In order to populate databases
  As a user
  I need to add entries to databases

  @javascript
  Scenario: Students can add entries to a database
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
    And I log in as "teacher1"
    And I follow "Course 1"
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name | Test field name |
      | Field description | Test field description |
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name | Test field 2 name |
      | Field description | Test field 2 description |
    # To generate the default templates.
    And I follow "Templates"
    And I wait until the page is ready
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I add an entry to "Test database name" database with:
      | Test field name | Student original entry |
      | Test field 2 name | Student original entry 2 |
    And I press "Save and view"
    Then I should see "Student original entry"
    And I follow "Edit"
    And I set the following fields to these values:
      | Test field name | Student original entry |
      | Test field 2 name |  |
    And I press "Save and view"
    Then I should not see "Student original entry 2"
    And I follow "Edit"
    And I set the following fields to these values:
      | Test field name | Student edited entry |
    And I press "Save and view"
    And I should see "Student edited entry"
    And I add an entry to "Test database name" database with:
      | Test field name | Student second entry |
    And I press "Save and add another"
    And the field "Test field name" does not match value "Student second entry"
    And I add an entry to "Test database name" database with:
      | Test field name | Student third entry |
    And I press "Save and view"
    And I follow "View list"
    And I should see "Student edited entry"
    And I should see "Student second entry"
    And I should see "Student third entry"
    # Will delete the first one.
    And I follow "Delete"
    And I press "Delete"
    And I should not see "Student edited entry"
    And I should see "Student second entry"
    And I should see "Student third entry"
    # Now I will bulk delete the rest of the entries.
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test database name"
    And I press "Select all"
    And I press "Delete selected"
    And I press "Delete"
    And I should see "No entries in database"