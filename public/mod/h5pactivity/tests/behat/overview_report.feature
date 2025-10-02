@mod @mod_h5pactivity
Feature: Testing overview integration in H5P activity
  In order to summarize the H5P activity
  As a user
  I need to be able to see the H5P activity overview

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |
      | student2 | C1 | student        |
      | student3 | C1 | student        |
    And the following "activity" exists:
      | course          | C1                                    |
      | activity        | h5pactivity                           |
      | name            | H5P activity                          |
      | intro           | description                           |
      | packagefilepath | h5p/tests/fixtures/find-the-words.h5p |
      | idnumber        | h5p                                   |
      | completion      | 1                                     |
      | enabletracking  | 1                                     |
      | reviewmode      | 1                                     |
      | grademethod     | 2                                     |
    And the following "activity" exists:
      | course          | C1                   |
      | activity        | h5pactivity          |
      | name            | Empty H5P activity   |
      | intro           | empty                |
      | idnumber        | empty                |
    And the following "mod_h5pactivity > attempts" exist:
      | user     | h5pactivity  | attempt | interactiontype | rawscore | maxscore | duration | completion | success |
      # student1.
      | student1 | H5P activity | 1       | choice          | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P activity | 1       | compound        | 2        | 2        | 4        | 1          | 1       |
      | student1 | H5P activity | 2       | choice          | 0        | 2        | 1        | 1          | 0       |
      | student1 | H5P activity | 2       | compound        | 0        | 2        | 4        | 1          | 0       |
      | student1 | H5P activity | 3       | matching        | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P activity | 3       | compound        | 2        | 2        | 4        | 1          | 1       |
      | student1 | H5P activity | 4       | true-false      | 2        | 2        | 1        | 1          | 1       |
      | student1 | H5P activity | 4       | compound        | 2        | 2        | 4        | 1          | 1       |
      # student2.
      | student2 | H5P activity | 1       | compound        | 0        | 2        | 1        | 1          | 0       |
    # We need to navigate to the activity to deploy the H5P file.
    And I am on the "H5P activity" "h5pactivity activity" page logged in as admin
    And I log out

  Scenario: The H5P activity overview report should generate log events
    Given I am on the "Course 1" "course > activities > h5pactivity" page logged in as "teacher1"
    And I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    When I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'h5pactivity'"

  @javascript
  Scenario: Students can see relevant columns in the H5P activity overview
    Given I am on the "Course 1" "course > activities > h5pactivity" page logged in as student1
    # Check columns.
    When I should see "Status" in the "h5pactivity_overview_collapsible" "region"
    # Check column values.
    Then the following should exist in the "Table listing all H5P activities" table:
      | Name               | Attempts | Grade |
      | H5P activity       | 4        | 75.00 |
      | Empty H5P activity | 0        | -     |

  @javascript
  Scenario: Teachers can see relevant columns in the H5P activity overview
    Given I am on the "Course 1" "course > activities > h5pactivity" page logged in as teacher1
    # Check columns.
    And I should see "Total attempts" in the "h5pactivity_overview_collapsible" "region"
    # Check column values.
    And the following should exist in the "Table listing all H5P activities" table:
      | Name               | H5P type         | Students who attempted | Total attempts | Actions |
      | H5P activity       | Find The Words   | 2 of 3                 | 5              | View    |
      | Empty H5P activity | Unknown H5P type | 0 of 3                 | 0              | View    |
    When I click on "5" "button" in the "H5P activity" "table_row"
    Then I should see "Grading method: Average grade"
    And I should see "Average attempts per student: 2.5"
    And I press the escape key
    And I click on "0" "button" in the "Empty H5P activity" "table_row"
    And I should see "Grading method: Highest grade"
    And I should see "Average attempts per student: 0"
    # Close the dropdown.
    And I press the escape key
    # Check the View link.
    And I click on "View" "link" in the "H5P activity" "table_row"
    And I should see "Attempts (5)"

  Scenario: The H5P activity index redirect to the activities overview
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Activities" block
    When I click on "H5P" "link" in the "Activities" "block"
    Then I should see "An overview of all activities in the course"
    And I should see "Name" in the "h5pactivity_overview_collapsible" "region"
    And I should see "H5P type" in the "h5pactivity_overview_collapsible" "region"
    And I should see "Actions" in the "h5pactivity_overview_collapsible" "region"
