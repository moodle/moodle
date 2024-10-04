@mod @mod_book @core_completion
Feature: View activity completion information in the book activity
  In order to have visibility of book completion requirements
  As a student
  I need to be able to view my book completion progress

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
      | activity       | book          |
      | course         | C1            |
      | idnumber       | mh1           |
      | name           | Music history |
      | completion     | 2             |
      | completionview | 1             |

  Scenario: View automatic completion items
    Given the following "mod_book > chapter" exists:
      | book    | Music history           |
      | title   | Drum theory             |
      | content | Rudiments are important |
    And I am on the "Music history" "book activity" page logged in as teacher1
    And "Music history" should have the "View" completion condition
    # Student view.
    When I am on the "Music history" "book activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"

  Scenario: View automatic completion items with last section hidden
    Given the following "activity" exists:
      | activity       | book        |
      | course         | C1          |
      | idnumber       | arth1       |
      | name           | Art history |
      | completion     | 2           |
      | completionview | 1           |
    And the following "mod_book > chapters" exist:
      | book        | title          | content        | pagenum | subchapter | hidden |
      | Art history | First chapter  | First chapter  | 1       | 0          | 0      |
      | Art history | Second chapter | Second chapter | 2       | 0          | 0      |
      | Art history | Sub chapter 1  | Sub chapter    | 3       | 1          | 0      |
      | Art history | Sub chapter 2  | Sub chapter    | 4       | 1          | 0      |
      | Art history | Sub chapter 3  | Sub chapter    | 5       | 1          | 1      |
    When I am on the "Art history" "book activity" page logged in as student1
    And I should see "First chapter"
    And the "View" completion condition of "Art history" is displayed as "todo"
    And I follow "Next"
    And I should see "Second chapter"
    And the "View" completion condition of "Art history" is displayed as "todo"
    And I follow "Next"
    And I should see "Sub chapter 1"
    And the "View" completion condition of "Art history" is displayed as "todo"
    And I follow "Next"
    And I should see "Sub chapter 2"
    And I should not see "Next"
    Then the "View" completion condition of "Art history" is displayed as "done"

  @javascript
  Scenario: A student can manually mark the book activity as done but a teacher cannot
    Given I am on the "Music history" "book activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Students must manually mark the activity as done" to "1"
    And I press "Save and display"
    And I set the following fields to these values:
      | Chapter title | Drum theory             |
      | Content       | Rudiments are important |
    And I press "Save changes"
    And I am on the "Music history" "book activity" page
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    # Student view.
    Given I am on the "Music history" "book activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
