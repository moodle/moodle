@core @core_my @javascript
Feature: Dashboard stops offering block moves once editing has been switched off
  In order to avoid moves that the server rejects and that are lost
  As a user
  I need the Dashboard to show its current editing state when I come back to it

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |

  Scenario: Going back to a Dashboard that was in editing mode shows its current state
    Given I log in as "student1"
    And I turn editing mode on
    And "Move Course overview block" "menuitem" should exist
    # Viewing a course as a student switches editing off for the whole session.
    And I am on the "C1" "Course" page
    When I press the "back" button in the browser
    Then I wait until "Move Course overview block" "menuitem" does not exist
    And I should not see "Reset page to default"
