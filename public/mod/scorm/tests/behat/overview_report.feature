@mod @mod_scorm
Feature: Testing overview integration in mod_scorm
  In order to summarize the scorm activities
  As a user
  I need to be able to see the scorm overview

  Background:
    Given the following "users" exist:
      | username        | firstname      | lastname |
      | student1        | Username       | 1        |
      | student2        | Username       | 2        |
      | student3        | Username       | 3        |
      | student4        | Username       | 4        |
      | student5        | Username       | 5        |
      | student6        | Username       | 6        |
      | student7        | Username       | 7        |
      | student8        | Username       | 8        |
      | teacher1        | Teacher        | T        |
      | editingteacher1 | EditingTeacher | T        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
      | Course 2 | C2        |
    And the following "course enrolments" exist:
      | user            | course | role           |
      | student1        | C1     | student        |
      | student2        | C1     | student        |
      | student3        | C1     | student        |
      | teacher1        | C1     | teacher        |
      | editingteacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name    | packagefilepath                             | forcenewattempt | idnumber | timeclose      | grademethod |
      | scorm    | C1     | Scorm 1 | mod/scorm/tests/packages/singlescobasic.zip | 0               | scorm1   | 1 January 2040 | 3           |
      | scorm    | C1     | Scorm 2 | mod/scorm/tests/packages/singlescobasic.zip | 0               | scorm2   |                | 3           |
      | scorm    | C2     | Scorm 3 | mod/scorm/tests/packages/singlescobasic.zip | 0               | scorm3   |                | 3           |
    And the following "mod_scorm > attempts" exist:
      | scorm  | user     | attempt | element               | value     | scoidentifier |
      | scorm1 | student1 | 1       | cmi.core.score.raw    | 50        | item_1        |
      | scorm1 | student1 | 1       | cmi.completion_status | completed | item_1        |
      | scorm1 | student2 | 1       | cmi.core.score.raw    | 100       | item_1        |
      | scorm1 | student2 | 1       | cmi.completion_status | completed | item_1        |

  Scenario: The scorm overview report should generate log events
    Given I am on the "Course 1" "course > activities > scorm" page logged in as "editingteacher1"
    When I am on the "Course 1" "course" page logged in as "editingteacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'scorm'"

  @javascript
  Scenario: Teachers can see relevant columns in the scorm overview
    When I am on the "Course 1" "course > activities > scorm" page logged in as "teacher1"
    Then I should see "Name" in the "scorm_overview_collapsible" "region"
    And I should see "Due date" in the "scorm_overview_collapsible" "region"
    And I should see "Student who attempted" in the "scorm_overview_collapsible" "region"
    And I should see "Total attempts" in the "scorm_overview_collapsible" "region"
    And I should see "Actions" in the "scorm_overview_collapsible" "region"
    Then the following should exist in the "Table listing all SCORM package activities" table:
      | Name    | Due date                         | Student who attempted | Total attempts | Actions |
      | Scorm 1 | Sunday, 1 January 2040, 12:00 AM | 2 of 5                | 2              | View    |
      | Scorm 2 | -                                | 0 of 5                | 0              | View    |

  @javascript
  Scenario: Teachers can see relevant columns when there are no participants in the course
    When I am on the "Course 2" "course > activities > scorm" page logged in as "admin"
    Then I should see "Name" in the "scorm_overview_collapsible" "region"
    And I should see "Student who attempted" in the "scorm_overview_collapsible" "region"
    And I should see "Total attempts" in the "scorm_overview_collapsible" "region"
    And I should see "Actions" in the "scorm_overview_collapsible" "region"
    Then the following should exist in the "Table listing all SCORM package activities" table:
      | Name    | Student who attempted | Total attempts | Actions |
      | Scorm 3 | 0 of 0                | 0              | View    |

  @javascript
  Scenario Template: Students can see relevant columns in the scorm overview
    When I am on the "Course 1" "course > activities > scorm" page logged in as "<student>"
    Then I should see "Name" in the "scorm_overview_collapsible" "region"
    And I should see "Due date" in the "scorm_overview_collapsible" "region"
    And I should see "Grade" in the "scorm_overview_collapsible" "region"
    Then the following should exist in the "Table listing all SCORM package activities" table:
      | Name    | Due date       | Grade   |
      | Scorm 1 | 1 January 2040 | <grade> |
      | Scorm 2 | -              | -       |
    Examples:
      | student  | grade  |
      | student1 | 50.00  |
      | student2 | 100.00 |
