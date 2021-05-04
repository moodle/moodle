@mod @mod_lti @core_completion @javascript
Feature: View activity completion information in the LTI activity
  In order to have visibility of LTI completion requirements
  As a student
  I need to be able to view my LTI completion progress

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
    And the following "activities" exist:
      | activity | name          | course | idnumber |
      | lti      | Music history | C1     | lti1     |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
      | Show activity completion conditions | Yes |
    And I press "Save and display"
    And I turn editing mode on
    When I open "Music history" actions menu
    And I click on "Edit settings" "link" in the "Music history" activity
    And I set the following fields to these values:
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view        | 1                                                 |
      | Require grade       | 1                                                 |
    And I press "Save and return to course"
    And I log out

  Scenario: View automatic completion items as a teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I follow "Music history"
    Then "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "View" completion condition

  Scenario: View automatic completion items as a student
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "todo"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "90.00" to the user "Vinnie Student1" for the grade item "Music history"
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Music history"
    Then the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "View" completion condition of "Music history" is displayed as "done"

  Scenario: Use manual completion
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I open "Music history" actions menu
    And I click on "Edit settings" "link" in the "Music history" activity
    And I expand all fieldsets
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I press "Save and display"
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
