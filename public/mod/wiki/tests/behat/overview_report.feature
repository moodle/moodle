@mod @mod_wiki
Feature: Testing overview integration in mod_wiki
  In order to summarize the wikis
  As a user
  I need to be able to see the wiki overview

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student1 | Student   | 1        |
      | student2 | Student   | 2        |
      | student3 | Student   | 3        |
      | teacher1 | Teacher   | T        |
    And the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 1         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    Given the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group 1 | C1     | G1       | 1             |
      | Group 2 | C1     | G2       | 1             |
      | Group 3 | C1     | G3       | 0             |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G2    |
      | student3 | G3    |
    And the following "activities" exist:
      | activity | course | name                     | idnumber | wikimode      | firstpagetitle       | groupmode |
      | wiki     | C1     | Separate wiki            | wiki1    | collaborative | Separate page 1      | 1         |
      | wiki     | C1     | Visible wiki             | wiki2    | collaborative | Visible page 1       | 2         |
      | wiki     | C1     | Collaborative wiki empty | wiki3    | collaborative | Collaborative page 1 |           |
      | wiki     | C1     | Individual wiki empty    | wiki4    | individual    | Individual page 1    |           |
    And the following wiki pages exist:
      | wiki  | title           | content      | group |
      | wiki1 | Separate page 1 | Group 1 page | G1    |
      | wiki1 | Separate page 1 | Group 2 page | G2    |
      | wiki2 | Visible page 1  | Group 1 page | G1    |
      | wiki2 | Visible page 1  | Group 2 page | G2    |
    And the following wiki pages exist:
      | wiki  | title           | content       |
      | wiki1 | Separate page 1 | No group page |
      | wiki2 | Visible page 1  | No group page |

  Scenario: The wiki overview report should generate log events
    Given I am on the "Course 1" "course > activities > wiki" page logged in as "teacher1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'wiki'"

  Scenario: Students can see relevant columns in the wiki overview
    Given I am on the "Course 1" "course > activities > wiki" page logged in as "student1"
    Then the following should exist in the "Table listing all Wiki activities" table:
      | Name                     | My entries | Total entries |
      | Separate wiki            | 0          | 0             |
      | Visible wiki             | 0          | 3             |
      | Collaborative wiki empty | 0          | 0             |
      | Individual wiki empty    | 0          | 0             |

  @javascript
  Scenario: Teachers can see relevant columns in the wiki overview
    When I am on the "Course 1" "course > activities > wiki" page logged in as "teacher1"
    Then the following should exist in the "Table listing all Wiki activities" table:
      | Name                     | Wiki mode          | Entries | Actions |
      | Separate wiki            | Collaborative wiki | 3       | View    |
      | Visible wiki             | Collaborative wiki | 3       | View    |
      | Collaborative wiki empty | Collaborative wiki | 0       | View    |
      | Individual wiki empty    | Individual wiki    | 0       | View    |
    And I click on "View" "link" in the "Separate wiki" "table_row"
    And I should see "Page list"
    And I am on the "Course 1" "course > activities > wiki" page
    And I click on "View" "link" in the "Collaborative wiki empty" "table_row"
    And I should not see "Page list"

  Scenario: The wiki index redirect to the activities overview
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Activities" block
    And I click on "Wikis" "link" in the "Activities" "block"
    Then I should see "An overview of all activities in the course"
    And I should see "Name" in the "wiki_overview_collapsible" "region"
    And I should see "Wiki mode" in the "wiki_overview_collapsible" "region"
    And I should see "Entries" in the "wiki_overview_collapsible" "region"
    And I should see "Actions" in the "wiki_overview_collapsible" "region"
