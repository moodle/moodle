@mod @mod_data
Feature: Users can view and search database entries
  In order to find the database entries that I am looking for
  As a user
  I need to list and search the database entries

  Scenario: Students can add view, list and search entries
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
    And I add an entry to "Test database name" database with:
      | Test field description | Teacher entry 1 |
    And I press "Save and add another"
    And I add an entry to "Test database name" database with:
      | Test field description | Teacher entry 2 |
    And I press "Save and add another"
    And I add an entry to "Test database name" database with:
      | Test field description | Teacher entry 3 |
    And I press "Save and view"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test database name"
    Then I should see "Teacher entry 1"
    And I should see "Teacher entry 2"
    And I should see "Teacher entry 3"
    And I follow "View single"
    And I should see "Teacher entry 1"
    And I should not see "Teacher entry 2"
    And "2" "link" should exist
    And "3" "link" should exist
    And I follow "Next"
    And I should see "Teacher entry 2"
    And I should not see "Teacher entry 1"
    And I follow "3"
    And I should see "Teacher entry 3"
    And I should not see "Teacher entry 2"
    And I follow "Previous"
    And I should see "Teacher entry 2"
    And I should not see "Teacher entry 1"
    And I should not see "Teacher entry 3"
    And I follow "Search"
    And I set the field "Test field name" to "Teacher entry 1"
    And I press "Save settings"
    And I should see "Teacher entry 1"
    And I should not see "Teacher entry 2"
    And I should not see "Teacher entry 3"
    And I follow "Search"
    And I set the field "Test field name" to "Teacher entry"
    And I set the field "Order" to "Descending"
    And I press "Save settings"
    And "Teacher entry 3" "text" should appear before "Teacher entry 2" "text"
    And "Teacher entry 2" "text" should appear before "Teacher entry 1" "text"
