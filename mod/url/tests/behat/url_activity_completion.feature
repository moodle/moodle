@mod @mod_url @core_completion
Feature: View activity completion information in the URL resource
  In order to have visibility of URL completion requirements
  As a student
  I need to be able to view my URL completion progress

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
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show completion conditions | Yes |
    And I press "Save and display"

  Scenario: View automatic completion items in automatic display mode
    Given I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" and I fill the form with:
      | Name                | Music history                                     |
      | External URL        | https://moodle.org/                               |
      | Display             | Automatic                                         |
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view        | 1                                                 |
    # Teacher view.
    And I follow "Music history"
    And "Music history" should have the "View" completion condition
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    Then the "View" completion condition of "Music history" is displayed as "done"

  Scenario: View automatic completion items in embed display mode
    Given I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" and I fill the form with:
      | Name                | Music history                                     |
      | External URL        | https://moodle.org/                               |
      | Display             | Embed                                             |
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view        | 1                                                 |
    # Teacher view.
    And I follow "Music history"
    And "Music history" should have the "View" completion condition
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    Then the "View" completion condition of "Music history" is displayed as "done"

  Scenario: View automatic completion items in open display mode
    Given I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" and I fill the form with:
      | Name                | Music history                                     |
      | External URL        | https://moodle.org/                               |
      | Display             | Open                                              |
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view        | 1                                                 |
    # Teacher view.
    And I follow "Music history"
    And "Music history" should have the "View" completion condition
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    Then the "View" completion condition of "Music history" is displayed as "done"

  Scenario: View automatic completion items in pop-up display mode
    Given I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" and I fill the form with:
      | Name                | Music history                                     |
      | External URL        | https://moodle.org/                               |
      | Display             | In pop-up                                         |
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view        | 1                                                 |
    # Teacher view.
    And I follow "Music history"
    And "Music history" should have the "View" completion condition
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    Then the "View" completion condition of "Music history" is displayed as "done"

  @javascript
  Scenario: Use manual completion
    Given I am on "Course 1" course homepage with editing mode on
    And I add a "URL" to section "1" and I fill the form with:
      | Name                | Music history                                        |
      | External URL        | https://moodle.org/                                  |
      | Display             | Automatic                                            |
      | Completion tracking | Students can manually mark the activity as completed |
    # Teacher view.
    And I follow "Music history"
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
