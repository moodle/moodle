@mod @mod_folder @core_completion
Feature: View activity completion information in the folder activity
  In order to have visibility of folder completion requirements
  As a student
  I need to be able to view my folder completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 1                | 1                        |
      | Course 2 | C2        | 1                | 0                        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | teacher1 | C1 | editingteacher |
      | student1 | C2 | student        |

  Scenario: View automatic completion items
    Given the following "activity" exists:
      | activity       | folder        |
      | course         | C1            |
      | idnumber       | mh1           |
      | name           | Music history |
      | section        | 1             |
      | completion     | 2             |
      | completionview | 1             |
    # Teacher view.
    And I am on the "Music history" "folder activity" page logged in as teacher1
    And "Music history" should have the "View" completion condition
    And I log out
    # Student view.
    When I am on the "Music history" "folder activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given the following "activity" exists:
      | activity       | folder        |
      | course         | C1            |
      | idnumber       | mh1           |
      | name           | Music history |
      | section        | 1             |
      | completion     | 1             |
    And I am on the "Music history" "folder activity" page logged in as teacher1
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    When I am on the "Music history" "folder activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"

  @javascript
  Scenario: The manual completion button will be shown on the course page for Inline on a course page display mode
    Given the following "activity" exists:
      | activity   | folder        |
      | course     | C2            |
      | idnumber   | mh2           |
      | name       | Music history |
      | section    | 1             |
      | completion | 1             |
      | display    | 1             |
    When I am on the "Course 2" course page logged in as student1
    Then the manual completion button for "Music history" should exist
    And the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
