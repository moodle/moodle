@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe @javascript @core_completion
Feature: Pass grade activity completion information in the h5p activity

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Vinnie    | Student1 | student1@example.com |
      | student2 | Vinnie    | Student2 | student2@example.com |
      | student3 | Vinnie    | Student3 | student3@example.com |
      | teacher1 | Darrell   | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | Enable completion tracking          | Yes |
      | Show activity completion conditions | Yes |
    And I press "Save and display"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name                | Music history                                     |
      | Completion tracking | Show activity as complete when conditions are met |
      | Require view        | 1                                                 |
      | Require grade       | 1                                                 |
      | completionpassgrade | 1                                                 |
      | gradepass           | 25                                                |
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I log out

  Scenario: View automatic completion items
    # Teacher view.
    Given I am on the "Music history" "h5pactivity activity" page logged in as teacher1
#    Given I log in as "teacher1"
#    And I am on "Course 1" course homepage
#    And I follow "Music history"
    And "Music history" should have the "View" completion condition
    And "Music history" should have the "Receive a grade" completion condition
    And "Music history" should have the "Receive a passing grade" completion condition
    And I log out
    # Student view.
    When I am on the "Music history" "h5pactivity activity" page logged in as student1
#    When I log in as "student1"
#    And I am on "Course 1" course homepage
#    And I follow "Music history"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I reload the page
    And I log out
    And I am on the "Music history" "h5pactivity activity" page logged in as student2
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Brasilia"
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 2 of 4\")]" to "Washington"
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 3 of 4\")]" to "Berlin"
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 4 of 4\")]" to "Canberra"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I switch to the main frame
    And I reload the page
    Then the "View" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "done"
    And I log out
    And I am on the "Music history" "h5pactivity activity" page logged in as student1
    And the "View" completion condition of "Music history" is displayed as "done"
    And the "Receive a grade" completion condition of "Music history" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Music history" is displayed as "failed"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And "Vinnie Student1" user has completed "Music history" activity
    And "Vinnie Student2" user has completed "Music history" activity
    And "Vinnie Student3" user has not completed "Music history" activity
