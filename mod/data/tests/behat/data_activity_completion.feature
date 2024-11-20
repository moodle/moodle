@mod @mod_data @core_completion @javascript
Feature: View activity completion in the database activity
  In order to have visibility of database completion requirements
  As a student
  I need to be able to view my database completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | teacher1 | C1 | editingteacher |
    And the following "activity" exists:
      | activity                 | data          |
      | course                   | C1            |
      | idnumber                 | mh1           |
      | name                     | Music history |
      | section                  | 1             |
      | completionentriesenabled | 1             |
      | completionentries        | 2             |
    And the following "mod_data > fields" exist:
      | database | type | name             |
      | mh1      | text | Instrument types |
    And the following "mod_data > templates" exist:
      | database | name            |
      | mh1      | singletemplate  |
      | mh1      | listtemplate    |
      | mh1      | addtemplate     |
      | mh1      | asearchtemplate |
      | mh1      | rsstemplate     |
    Given I am on the "Music history" "data activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Aggregate type           | Average of ratings |
      | scale[modgrade_type]     | Point              |
      | scale[modgrade_point]    | 100                |
      | Add requirements         | 1                  |
      | View the activity        | 1                  |
      | Receive a grade          | 1                  |
      | Any grade                | 1                  |
    And I press "Save and display"
    And I log out

  Scenario: View automatic completion items as a teacher
#   We add an entry to let the user change to a different view.
    Given the following "mod_data > entries" exist:
      | database | user     | Instrument types |
      | mh1      | teacher1 | Drums            |
    When I am on the "Music history" "data activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Make entries: 2" completion condition
    And "Music history" should have the "Receive a grade" completion condition
    And I select "Single view" from the "jump" singleselect
    And "Music history" should have the "View" completion condition
    And "Music history" should have the "Make entries: 2" completion condition
    And "Music history" should have the "Receive a grade" completion condition

  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "data activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the following "mod_data > entries" exist:
      | database | user     | Instrument types |
      | mh1      | student1 | Drums            |
    And I am on "Course 1" course homepage
    # One entry is not enough to mark as complete.
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the following "mod_data > entries" exist:
      | database | user     | Instrument types |
      | mh1      | student1 | Hurdygurdy       |
    And I am on "Course 1" course homepage
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I log out

    And I am on the "Music history" "data activity" page logged in as teacher1
    And I select "Single view" from the "jump" singleselect
    And I set the field "rating" to "3"
    And I log out

    When I am on the "Music history" "data activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And I log out
    When I am on the "Course 1" course page logged in as teacher1
    And "Vinnie Student1" user has completed "Music history" activity

  @javascript
  Scenario: Use manual completion
    Given I am on the "Music history" "data activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Students must manually mark the activity as done" to "1"
    And I press "Save and display"
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    When I am on the "Music history" "data activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
