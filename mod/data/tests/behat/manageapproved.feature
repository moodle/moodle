@mod @mod_data
Feature: Users can edit approved entries in database activities
  In order to control whether approved database entries can be changed
  As a teacher
  I need to be able to enable or disable management of approved entries

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

  @javascript
  Scenario: Students can manage their approved entries to a database
    Given the following "activity" exists:
      | activity       | data               |
      | course         | C1                 |
      | idnumber       | Test database name |
      | name           | Test database name |
      | approval       | 1                  |
      | manageapproved | 1                  |
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I add a "Short text" field to "Test database name" database and I fill the form with:
      | Field name | Test field name |
      | Field description | Test field description |
    # To generate the default templates.
    And I navigate to "Templates" in current page administration
    And I log out
    # Add an entry as a student.
    And I am on the "Test database name" "data activity" page logged in as student1
    And I add an entry to "Test database name" database with:
      | Test field name | Student entry |
    And I press "Save"
    And I log out
    # Approve the student's entry as a teacher.
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I follow "Approve"
    And I log out
    # Make sure the student can still edit their entry after it's approved.
    When I am on the "Test database name" "data activity" page logged in as student1
    Then I should see "Student entry"
    And "Edit" "link" should exist

  @javascript
  Scenario: Students can not manage their approved entries to a database
    # Create database activity and don't allow editing of approved entries.
    Given the following "activity" exists:
      | activity       | data               |
      | course         | C1                 |
      | idnumber       | Test database name |
      | name           | Test database name |
      | approval       | 1                  |
      | manageapproved | 0                  |
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I add a "Short text" field to "Test database name" database and I fill the form with:
      | Field name | Test field name |
      | Field description | Test field description |
    # To generate the default templates.
    And I navigate to "Templates" in current page administration
    And I log out
    # Add an entry as a student.
    And I am on the "Test database name" "data activity" page logged in as student1
    And I add an entry to "Test database name" database with:
      | Test field name | Student entry |
    And I press "Save"
    And I log out
    # Approve the student's entry as a teacher.
    And I am on the "Test database name" "data activity" page logged in as teacher1
    And I follow "Approve"
    And I log out
    # Make sure the student isn't able to edit their entry after it's approved.
    When I am on the "Test database name" "data activity" page logged in as student1
    Then "Edit" "link" should not exist
    And I should see "Student entry"
