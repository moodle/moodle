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
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show activity completion conditions | Yes |
    And I press "Save and display"
    And the following "activity" exists:
      | activity | data          |
      | course   | C1            |
      | idnumber | mh1           |
      | name     | Music history |
      | section  | 1             |
    And I am on the "Music history" "data activity" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Aggregate type           | Average of ratings                                |
      | scale[modgrade_type]     | Point                                             |
      | scale[modgrade_point]    | 100                                               |
      | gradepass                | 50                                                |
      | Completion tracking      | Show activity as complete when conditions are met |
      | Require view             | 1                                                 |
      | Require grade            | 1                                                 |
      | completionpassgrade      | 1                                                 |
      | completionentriesenabled | 1                                                 |
      | completionentries        | 2                                                 |
    And I press "Save and display"
    And I add a "Text input" field to "Music history" database and I fill the form with:
      | Field name | Instrument types |
    And I navigate to "Templates" in current page administration
    And I press "Save template"
    And I log out

  Scenario: View automatic completion items as a teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I follow "Music history"
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
  Scenario: View automatic completion items as a failing student
    Given I am on the "Music history" "data activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And I am on "Course 1" course homepage
    And I add an entry to "Music history" database with:
      | Instrument types | Drums |
    And I press "Save"
    # One entry is not enough to mark as complete.
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And I am on "Course 1" course homepage
    And I add an entry to "Music history" database with:
      | Instrument types | Hurdygurdy |
    And I press "Save"
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
  Scenario: View automatic completion items as a passing student
    Given I am on the "Music history" "data activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And I am on "Course 1" course homepage
    And I add an entry to "Music history" database with:
      | Instrument types | Drums |
    And I press "Save"
    # One entry is not enough to mark as complete.
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 2" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And I am on "Course 1" course homepage
    And I add an entry to "Music history" database with:
      | Instrument types | Hurdygurdy |
    And I press "Save"
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
