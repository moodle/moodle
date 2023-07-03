@mod @mod_chat @core_completion
Feature: View activity completion information in the chat activity
  In order to have visibility of chat completion requirements
  As a student
  I need to be able to view my chat completion progress

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user | course | role           |
      | student1 | C1 | student        |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show activity completion conditions | Yes |
    And I press "Save and display"
    And I log out

  Scenario: View automatic completion items
    Given I log in as "teacher1"
    And the following "activity" exists:
      | activity       | chat          |
      | course         | C1            |
      | name           | Music history |
      | section        | 1             |
      | completion     | 2             |
      | completionview | 1             |
    And I am on "Course 1" course homepage
    # Teacher view.
    And I am on the "Music history" Activity page
    And "Music history" should have the "View" completion condition
    And I log out
    # Student view.
    And I am on the "Music history" Activity page logged in as student1
    Then the "View" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given I log in as "teacher1"
    And the following "activity" exists:
      | activity       | chat          |
      | course         | C1            |
      | name           | Music history |
      | section        | 1             |
      | completion     | 1             |
    And I am on "Course 1" course homepage
    # Teacher view.
    And "Music history" should have the "Mark as done" completion condition
    And I log out
    # Student view.
    And I am on the "Music history" Activity page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
