@mod @mod_assign @core_completion
Feature: View activity completion information in the Assign resource
  In order to have visibility of assign completion requirements
  As a student
  I need to be able to view my assign completion progress

  Background:
    Given the following "users" exist:
      | username | firstname  | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"

  @javascript
  Scenario: The manual completion button will be shown on the course page if the Show completion conditions is set to Yes
    Given I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show completion conditions | Yes |
    And I press "Save and display"
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name     | History of ants                                      |
      | Completion tracking | Students can manually mark the activity as completed |
    # Teacher view.
    And the manual completion button for "History of ants" should exist
    And the manual completion button for "History of ants" should be disabled
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then the manual completion button for "History of ants" should exist
    And the manual completion button of "History of ants" is displayed as "Mark as done"
    And I toggle the manual completion state of "History of ants"
    And the manual completion button of "History of ants" is displayed as "Done"

  @javascript
  Scenario: The manual completion button will not be shown on the course page if the Show completion conditions is set to No
    Given I am on "Course 1" course homepage with editing mode on
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show completion conditions | No  |
    And I press "Save and display"
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name     | History of ants                                      |
      | Completion tracking | Students can manually mark the activity as completed |
    # Teacher view.
    And the manual completion button for "History of ants" should not exist
    And I log out
    # Student view.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    Then the manual completion button for "History of ants" should not exist
