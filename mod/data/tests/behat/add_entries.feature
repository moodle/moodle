@mod @mod_data
Feature: Users can add entries to database activities
  In order to populate databases
  As a user
  I need to add entries to databases

  Scenario: Students can add entries to a database
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
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
    # To generate the default templates.
    And I follow "Templates"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I add an entry to "Test database name" database with:
      | Test field description | Student original entry |
    And I press "Save and view"
    Then I should see "Student original entry"
    And I follow "Edit"
    And I set the following fields to these values:
      | Test field description | Student edited entry |
    And I press "Save and view"
    And I should see "Student edited entry"
    And I add an entry to "Test database name" database with:
      | Test field description | Student second entry |
    And I press "Save and add another"
    And I add an entry to "Test database name" database with:
      | Test field description | Student third entry |
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
