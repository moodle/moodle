@mod @mod_page @core_completion
Feature: View activity completion information in the Page resource
  In order to have visibility of page completion requirements
  As a student
  I need to be able to view my page completion progress

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
    And the following "activity" exists:
      | activity    | page                     |
      | course      | C1                       |
      | idnumber    | page1                    |
      | name        | Music history            |
      | intro       | A lesson learned in life |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show activity completion conditions | Yes |
    And I press "Save and display"

  Scenario: View automatic completion items
    Given I am on "Course 1" course homepage with editing mode on
    And I follow "Music history"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Completion tracking  | Show activity as complete when conditions are met |
      | completionview       | 1                                                 |
    And I press "Save and return to course"
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
    And I follow "Music history"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Completion tracking | Students can manually mark the activity as completed |
    And I press "Save and return to course"
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

  Scenario: The manual completion button will not be shown on the course page if the Show activity completion conditions is set to No
    Given I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show activity completion conditions | No  |
    And I press "Save and display"
    And I follow "Music history"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Completion tracking | Students can manually mark the activity as completed |
    And I press "Save and return to course"
    # Teacher view.
    And the manual completion button for "Music history" should not exist
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then the manual completion button for "Music history" should not exist
