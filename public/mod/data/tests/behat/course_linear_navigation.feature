@mod @mod_data
Feature: Display the course linear navigation in the database activity pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in database activity pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name          | intro                    | course | idnumber |
      | data     | Database1     | Test database activity   | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type | name           | description        |
      | data1    | text | Entry Name     | Name of the entry  |
      | data1    | text | Entry Details  | Details of entry   |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | user     | Entry Name          | Entry Details          |
      | data1    | student1 | First Student Entry | This is the first test |
      | data1    | teacher1 | First Teacher Entry | This is teacher test   |

  @javascript
  Scenario: As a student I should see the course linear navigation in database pages that allow it
    Given I am on the "Database1" "data activity" page logged in as "student1"
    Then the course linear navigation should be visible
    And I click on "Add entry" "button"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Entry Name    | New Student Entry |
      | Entry Details | Testing new entry |
    And I press "saveandview"
    And I should see "New Student Entry"
    And the course linear navigation should be visible
    And I open the action menu in "region-main" "region"
    And I choose "Edit" in the open action menu
    And the course linear navigation should not be visible
    And I set the field "Entry Name" to "Updated Entry Name"
    And I press "Save"
    And the course linear navigation should be visible

  @javascript
  Scenario: As a teacher I should see the course linear navigation in database pages that allow it
    Given I am on the "Database1" "data activity" page logged in as "teacher1"
    Then the course linear navigation should be visible
    And I click on "Actions" "button"
    And I click on "Import entries" "link"
    And the course linear navigation should not be visible
    And I click on "Cancel" "button"
    And I click on "Actions" "button"
    And I click on "Export entries" "link"
    And the course linear navigation should not be visible
    And I click on "Cancel" "button"
    And I open the action menu in ".defaulttemplate-listentry" "css_element"
    And I choose "Delete" in the open action menu
    And the course linear navigation should not be visible
    And I click on "Delete" "button" in the "Confirm" "dialogue"
    And the course linear navigation should be visible
    And I click on "Add entry" "button"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Entry Name    | New Teacher Entry |
      | Entry Details | Teacher test data |
    And I press "saveandview"
    And the course linear navigation should be visible
    And I open the action menu in "#defaulttemplate-single" "css_element"
    And I choose "Edit" in the open action menu
    And the course linear navigation should not be visible
    And I press "Save"
    And the course linear navigation should be visible
    And I navigate to "Presets" in current page administration
    And the course linear navigation should not be visible
    And I navigate to "Fields" in current page administration
    And the course linear navigation should not be visible
    And I navigate to "Templates" in current page administration
    And the course linear navigation should not be visible
