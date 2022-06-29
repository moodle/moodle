@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe @javascript @core_completion
Feature: View activity completion information in the h5p activity
  In order to have visibility of h5p completion requirements
  As a student
  I need to be able to view my h5p completion progress

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
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name                | Music history                                     |
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view        | 1                                                 |
      | Require grade       | 1                                                 |
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I log out

  Scenario: View automatic completion items
    Given I am on the "Music history" "h5pactivity activity" page logged in as teacher1
    # Teacher view.
    And "Music history" should have the "View" completion condition
    And "Music history" should have the "Receive a grade" completion condition
    And I log out
    # Student view.
    When I am on the "Music history" "h5pactivity activity" page logged in as student1
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I reload the page
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"

  Scenario: Use manual completion
    Given I am on the "Music history" "h5pactivity activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I press "Save and display"
    # Teacher view.
    And I am on the "Music history" "h5pactivity activity" page
    And the manual completion button for "Music history" should be disabled
    And I log out
    # Student view.
    When I am on the "Music history" "h5pactivity activity" page logged in as student1
    Then the manual completion button of "Music history" is displayed as "Mark as done"
    And I toggle the manual completion state of "Music history"
    And the manual completion button of "Music history" is displayed as "Done"
