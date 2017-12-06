@mod @mod_data
Feature: Set entries required as a completion condition for a data item
  In order to ensure students make a minimum number of entries
  As a teacher
  I need to set entries required to mark the database activity as completed

  Scenario: Two entries required to complete the activity
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | C1        | 1                |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "C1"
    And I turn editing mode on
    And I add a "Database" to section "1" and I fill the form with:
      | Name | Test database name |
      | Description | Test database description |
      | Completion tracking | Show activity as complete when conditions are met |
      | completionview | 0 |
      | completionentriesenabled | checked |
      | completionentries        | 2 |
    And I follow "Course 1"
    And I add a "Text input" field to "Test database name" database and I fill the form with:
      | Field name | Test field name |
    And I follow "C1"
    And I log out
    When I log in as "student1"
    And I follow "C1"
    And I add an entry to "Test database name" database with:
      | Test field name | Student original entry |
    And I press "Save and view"
    And I follow "C1"
    And I log out
    And I log in as "teacher1"
    And I follow "C1"
    #One entry is not enough to mark as complete
    And "Student 1" user has not completed "Test database name" activity
    And I log out
    When I log in as "student1"
    And I follow "C1"
    And I add an entry to "Test database name" database with:
      | Test field name | Student second entry |
    And I press "Save and view"
    And I log out
    And I log in as "teacher1"
    And I follow "C1"
    Then "Student 1" user has completed "Test database name" activity
    And I follow "Course 1"
    And I follow "Test database name"
    And I navigate to "Edit settings" in current page administration
    And I press "Unlock completion"
    And I set the field "completionentries" to "1"
    And I press "Save and display"
    And I follow "C1"
    Then "Student 1" user has completed "Test database name" activity
    And I log out
