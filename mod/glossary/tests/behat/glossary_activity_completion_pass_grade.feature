@mod @mod_glossary @core_completion
Feature: Pass grade completion in the glossary activity
  In order to have visibility of glossary completion requirements
  As a student
  I need to be able to view my glossary completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | teacher1 | C1 | editingteacher |
    And the following "activity" exists:
      | activity | glossary      |
      | course   | C1            |
      | idnumber | mh1           |
      | name     | Music history |
      | section  | 1             |
    When I am on the "Music history" "glossary activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Aggregate type           | Average of ratings                                |
      | scale[modgrade_type]     | Point                                             |
      | scale[modgrade_point]    | 100                                               |
      | Ratings > Grade to pass  | 50                                                |
      | Add requirements         | 1                  |
      | View the activity        | 1                                                 |
      | Receive a grade          | 1                                                 |
      | Passing grade            | 1                                                 |
      | completionentriesenabled | 1                                                 |
      | completionentries        | 1                                                 |
    And I press "Save and display"
    And I log out

  Scenario: View automatic completion items as a teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I follow "Music history"
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Make entries: 1" completion condition
    And "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "Receive a passing grade" completion condition

  Scenario: View automatic completion items as a failing student
    Given I am on the "Music history" "glossary activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 1" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    When I am on "Course 1" course homepage
    And I follow "Music history"
    And I press "Add entry"
    And I set the following fields to these values:
      | Concept    | Blast beats                                               |
      | Definition | Repeated fast tempo hits combining bass, snare and cymbal |
    And I press "Save changes"
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 1" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And I log out
    And I am on the "Music history" "glossary activity" page logged in as teacher1
    And I set the field "rating" to "3"
    And I press "Rate"
    And I log out
    When I am on the "Music history" "glossary activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 1" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "failed"

  Scenario: View automatic completion items as a passing student
    Given I am on the "Music history" "glossary activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 1" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    When I am on "Course 1" course homepage
    And I follow "Music history"
    And I press "Add entry"
    And I set the following fields to these values:
      | Concept    | Blast beats                                               |
      | Definition | Repeated fast tempo hits combining bass, snare and cymbal |
    And I press "Save changes"
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 1" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "todo"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    And I set the field "rating" to "60"
    And I press "Rate"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 1" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "done"
