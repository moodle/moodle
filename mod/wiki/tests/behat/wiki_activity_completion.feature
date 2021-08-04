@mod @mod_wiki @core_completion
Feature: View activity completion information in the Wiki activity
  In order to have visibility of wiki completion requirements
  As a student
  I need to be able to view my wiki completion progress

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
      | activity       | wiki          |
      | course         | C1            |
      | idnumber       | mh1           |
      | name           | Music history |
      | section        | 1             |
      | completion     | 2             |
      | completionview | 1             |
    And I am on the "Music history" "wiki activity" page logged in as teacher1
    And I click on "Create page" "button"
    And I log out

  Scenario: View automatic completion items as a teacher and confirm all tabs display conditions
    When I am on the "Music history" "wiki activity" page logged in as teacher1
    Then "Music history" should have the "View" completion condition
    And I click on "Edit" "link" in the "region-main" "region"
    And "Music history" should have the "View" completion condition
    And I follow "Comments"
    And "Music history" should have the "View" completion condition
    And I follow "Map"
    And "Music history" should have the "View" completion condition
    And I follow "Files"
    And "Music history" should have the "View" completion condition
    And I follow "Administration"
    And "Music history" should have the "View" completion condition

  Scenario: View automatic completion items as a student
    When I am on the "Music history" "wiki activity" page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given I am on the "Music history" "wiki activity" page logged in as teacher1
    And I am on the "Music history" "wiki activity editing" page
    And I expand all fieldsets
    And I press "Unlock completion options"
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I press "Save and display"
    # Teacher view.
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    When I am on the "Music history" "wiki activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
