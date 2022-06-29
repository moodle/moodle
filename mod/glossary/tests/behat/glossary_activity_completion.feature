@mod @mod_glossary @core_completion
Feature: View activity completion in the glossary activity
  In order to have visibility of glossary completion requirements
  As a student
  I need to be able to view my glossary completion progress

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
      | activity | glossary      |
      | course   | C1            |
      | name     | Music history |
      | section  | 1             |
    And I am on the "Music history" "glossary activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the following fields to these values:
      | Aggregate type           | Average of ratings                                |
      | scale[modgrade_type]     | Point                                             |
      | scale[modgrade_point]    | 100                                               |
      | Completion tracking      | Show activity as complete when conditions are met |
      | Require view             | 1                                                 |
      | Require grade            | 1                                                 |
      | completionentriesenabled | 1                                                 |
      | completionentries        | 1                                                 |
    And I press "Save and display"
    And I log out

  Scenario: View automatic completion items as a teacher
    Given I am on the "Music history" "glossary activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition
    And "Music history" should have the "Make entries: 1" completion condition
    And "Music history" should have the "Receive a grade" completion condition

  Scenario: View automatic completion items as a student
    Given I am on the "Music history" "glossary activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 1" completion condition of "Music history" is displayed as "todo"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I am on the "Music history" "glossary activity" page
    And I press "Add entry"
    And I set the following fields to these values:
      | Concept    | Blast beats                                               |
      | Definition | Repeated fast tempo hits combining bass, snare and cymbal |
    And I press "Save changes"
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 1" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I log out
    And I am on the "Music history" "glossary activity" page logged in as teacher1
    And I set the field "rating" to "3"
    And I press "Rate"
    And I log out
    When I am on the "Music history" "glossary activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Make entries: 1" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given I am on the "Music history" "glossary activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I press "Save and display"
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    When I am on the "Music history" "glossary activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
