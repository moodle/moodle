@mod @mod_data @core_completion
Feature: Completion pass grade
  View activity completion in the database activity
  In order to have visibility of database completion requirements
  As a student
  I need to be able to view my database completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | student2 | Vinnie    | Student2 | student2@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activity" exists:
      | activity | data          |
      | course   | C1            |
      | idnumber | mh1           |
      | name     | Music history |
      | section  | 1             |
    And the following "mod_data > fields" exist:
      | database | type | name             |
      | mh1      | text | Instrument types |
    And I am on the "Music history" "data activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Aggregate type           | Average of ratings                                |
      | scale[modgrade_type]     | Point                                             |
      | scale[modgrade_point]    | 100                                               |
      | gradepass                | 50                                                |
      | Add requirements         | 1                                                 |
      | View the activity        | 1                                                 |
      | Receive a grade          | 1                                                 |
      | Passing grade            | 1                                                 |
      | completionentriesenabled | 1                                                 |
      | completionentries        | 2                                                 |
    And I press "Save and display"
    And I log out

  @javascript
  Scenario: Database module completion conditions are displayed regardless of the view
#   We add an entry to let the user change to a different view.
    Given the following "mod_data > entries" exist:
      | database | user     | Instrument types |
      | mh1      | teacher1 | Drums            |
    When I am on the "Music history" "data activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Make entries: 2" completion condition
    And "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "Receive a passing grade" completion condition
    And I select "Single view" from the "jump" singleselect
    And "Music history" should have the "View" completion condition
    And "Music history" should have the "Make entries: 2" completion condition
    And "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "Receive a passing grade" completion condition

  @javascript
  Scenario: Student cannot complete a database activity if one of the conditions are not met
    Given I am on the "Music history" "data activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And the following "mod_data > entries" exist:
      | database | user     | Instrument types |
      | mh1      | student1 | Drums            |
    And I am on "Course 1" course homepage
    # One entry is not enough to mark as complete.
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And the following "mod_data > entries" exist:
      | database | user     | Instrument types |
      | mh1      | student1 | Hurdygurdy       |
    And I am on "Course 1" course homepage
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And I log out
    And I am on the "Music history" "data activity" page logged in as teacher1
    And I select "Single view" from the "jump" singleselect
    And I set the field "rating" to "3"
    And I log out
    When I am on the "Music history" "data activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "failed"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And "Vinnie Student1" user has completed "Music history" activity

  @javascript
  Scenario: Student can complete a database activity when all conditions are met
    Given I am on the "Music history" "data activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And the following "mod_data > entries" exist:
      | database | user     | Instrument types |
      | mh1      | student1 | Drums            |
    And I am on "Course 1" course homepage
    # One entry is not enough to mark as complete.
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And the following "mod_data > entries" exist:
      | database | user     | Instrument types |
      | mh1      | student1 | Hurdygurdy       |
    And I am on "Course 1" course homepage
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And I log out
    And I am on the "Music history" "data activity" page logged in as teacher1
    And I select "Single view" from the "jump" singleselect
    And I set the field "rating" to "60"
    And I log out
    When I am on the "Music history" "data activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "done"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And "Vinnie Student1" user has completed "Music history" activity
