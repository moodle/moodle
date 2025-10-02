@mod @mod_data
Feature: Testing overview integration in database activity
  In order to summarize the database activity
  As a user
  I need to be able to see the database activity overview

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | course          | C1                   |
      | activity        | data                 |
      | name            | Database activity    |
      | intro           | description          |
      | idnumber        | data1                |
      | approval        | 1                    |
      | completion      | 1                    |
      | comments        | 1                    |
      | timeavailableto | ##1 Jan 2040 08:00## |
    And the following "activity" exists:
      | course          | C1                   |
      | activity        | data                 |
      | name            | Without comments     |
      | intro           | description          |
      | idnumber        | data2                |
      | approval        | 1                    |
      | completion      | 1                    |
      | comments        | 1                    |
      | timeavailableto | ##1 Jan 2040 08:00## |
    And the following "activity" exists:
      | course          | C1                   |
      | activity        | data                 |
      | name            | Empty database       |
      | intro           | empty database       |
      | idnumber        | data3                |
      | approval        | 0                    |
      | completion      | 0                    |
      | comments        | 0                    |
    And the following "mod_data > fields" exist:
      | database | type | name             | description                  |
      | data1    | text | Title field      | Title field description      |
      | data1    | text | Short text field | Short text field description |
      | data2    | text | Title field      | Title field description      |
      | data2    | text | Short text field | Short text field description |
    And the following "mod_data > templates" exist:
      | database | name            |
      | data1    | singletemplate  |
      | data1    | listtemplate    |
      | data1    | addtemplate     |
      | data1    | asearchtemplate |
      | data1    | rsstemplate     |
      | data2    | singletemplate  |
      | data2    | listtemplate    |
      | data2    | addtemplate     |
      | data2    | asearchtemplate |
      | data2    | rsstemplate     |
    And the following "mod_data > entries" exist:
      | database | user     | Title field           | Short text field | approved |
      | data1    | student1 | Student entry         | Approved         | 1        |
      | data1    | student1 | Student second entry  | Pending          | 0        |
      | data1    | teacher1 | Teacher entry         | Approved         | 1        |
      | data2    | teacher1 | Entry no comments     | Approved         | 1        |

  Scenario: The database activity overview report should generate log events
    Given I am on the "Course 1" "course > activities > data" page logged in as "teacher1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'data'"

  @javascript
  Scenario: Students can see relevant columns in the database activity overview
    # Add a comment to test the values.
    Given I am on the "Database activity" "data activity" page logged in as student1
    And I select "Single view" from the "jump" singleselect
    And I click on "Comments (0)" "link"
    And I set the following fields to these values:
      | Comment        | Commenting the entry |
    And I click on "Save comment" "link"
    When I am on the "Course 1" "course > activities > data" page
    # Check columns.
    Then I should see "Name" in the "data_overview_collapsible" "region"
    And I should see "Status" in the "data_overview_collapsible" "region"
    # Check column values.
    And the following should exist in the "Table listing all Database activities" table:
      | Name              | Due date       | Total entries | My entries | Comments  |
      | Database activity | 1 January 2040 | 2             | 2          | 1         |
      | Without comments  | 1 January 2040 | 1             | 0          | 0         |
      | Empty database    | -              | 0             | 0          | -         |

  @javascript
  Scenario: Teachers can see relevant columns in the database activity overview
    # Add a comment to test the values.
    Given I am on the "Database activity" "data activity" page logged in as teacher1
    And I select "Single view" from the "jump" singleselect
    And I click on "Comments (0)" "link"
    And I set the following fields to these values:
      | Comment        | Commenting the entry |
    And I click on "Save comment" "link"
    When I am on the "Course 1" "course > activities > data" page
    # Check columns.
    And I should not see "My entries" in the "data_overview_collapsible" "region"
    And I should not see "Total entries" in the "data_overview_collapsible" "region"
    # Check column values.
    Then the following should exist in the "Table listing all Database activities" table:
      | Name              | Due date       | Entries | Comments | Actions     |
      | Database activity | 1 January 2040 | 3       | 1        | Approve (1) |
      | Without comments  | 1 January 2040 | 1       | 0        | View        |
      | Empty database    | -              | 0       | -        | View        |
    # Check the Approve link.
    And I click on "Approve" "link" in the "data_overview_collapsible" "region"
    And I should see "Pending approval"

  Scenario: The database activity index redirect to the activities overview
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Activities" block
    And I click on "Database" "link" in the "Activities" "block"
    Then I should see "An overview of all activities in the course"
    And I should see "Name" in the "data_overview_collapsible" "region"
    And I should see "Due date" in the "data_overview_collapsible" "region"
    And I should see "Actions" in the "data_overview_collapsible" "region"
