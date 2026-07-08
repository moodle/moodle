@mod @mod_scorm
Feature: Display the course linear navigation in the SCORM pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in SCORM pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student  | Student   | Lastname |
      | teacher  | Teacher   | Lastname |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name                   | packagefilepath                                | idnumber | skipview | popup |
      | scorm    | C1     | SCORM1                 | mod/scorm/tests/packages/singlesco_scorm12.zip | scorm1   | 0        | 0     |
      | scorm    | C1     | SCORM2 - New window    | mod/scorm/tests/packages/singlesco_scorm12.zip | scorm2   | 0        | 1     |
      | scorm    | C1     | SCORM3 - Skip view     | mod/scorm/tests/packages/singlesco_scorm12.zip | scorm3   | 2        | 0     |
      | scorm    | C1     | SCORM4 - Skip + Window | mod/scorm/tests/packages/singlesco_scorm12.zip | scorm4   | 2        | 1     |
    And the following "mod_scorm > attempts" exist:
      | scorm  | user    | attempt | element               | value     | scoidentifier |
      | scorm1 | student | 1       | cmi.core.score.raw    | 50        | item_1        |
      | scorm1 | student | 1       | cmi.completion_status | completed | item_1        |

  @javascript
  Scenario: As a student I should see the scorm linear navigation when opened in the current window
    When I am on the "SCORM1" "scorm activity" page logged in as "student"
    Then the course linear navigation should be visible
    # Open the SCORM in the current window.
    And I press "Enter"
    And the course linear navigation should not be visible
    And I follow "Exit activity"
    And the course linear navigation should be visible
    And I should see "SCORM1" in the "page" "region"
    # Preview.
    And I press "Preview"
    And the course linear navigation should not be visible
    And I follow "Exit activity"
    And the course linear navigation should be visible

  @javascript
  Scenario: As a student I should see the scorm linear navigation when opened in a new window
    When I am on the "SCORM2 - New window" "scorm activity" page logged in as "student"
    Then the course linear navigation should be visible
    # Open the SCORM in a new window.
    And I press "Enter"
    And I switch to a second window
    And the course linear navigation should not be visible
    And I close all opened windows
    And the course linear navigation should be visible
    And I should see "SCORM2 - New window" in the "page" "region"

  @javascript
  Scenario: As a student I should see the scorm linear navigation when it is set to skip view
    Given I am on the "C1" course page logged in as "student"
    When I follow "SCORM3 - Skip view"
    Then the course linear navigation should not be visible
    And I follow "Exit activity"
    And the course linear navigation should be visible
    And I should see "SCORM3 - Skip view" in the "page" "region"

  @javascript
  Scenario: As a student I should see the scorm linear navigation when it is set to skip view and open in a new window
    Given I am on the "C1" course page logged in as "student"
    When I follow "SCORM4 - Skip + Window"
    And I switch to a second window
    Then the course linear navigation should not be visible
    And I close all opened windows
    And the course linear navigation should be visible
    And I should see "SCORM4 - Skip + Window" in the "page" "region"
    And I should not see "This SCORM package has been launched in a popup window"

  @javascript
  Scenario: As a teacher I should see the course linear navigation in scorm pages that allow it
    Given the following "role capability" exists:
      | role                         | editingteacher |
      | mod/scorm:deleteownresponses | allow          |
    When I am on the "SCORM1" "scorm activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    And I press "Enter"
    And the course linear navigation should not be visible
    And I follow "Exit activity"
    # Delete own responses.
    And I press "Delete all SCORM attempts"
    And the course linear navigation should not be visible
    And I press "Cancel"
    # Reports.
    And I navigate to "Reports" in current page administration
    And the course linear navigation should not be visible
    And I select "Interactions report" from the "jump" singleselect
    And the course linear navigation should not be visible
    And I click on "Not attempted" "link" in the "Teacher Lastname" "table_row"
    And the course linear navigation should not be visible
    And I click on "Back" "link" in the ".tertiary-navigation" "css_element"
    And I click on "1" "link" in the "Student Lastname" "table_row"
    And the course linear navigation should not be visible
    And I select "Interactions" from the "jump" singleselect
    And the course linear navigation should not be visible
