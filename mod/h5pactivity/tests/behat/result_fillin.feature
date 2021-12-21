@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe @javascript
Feature: View fill the blanks attempt report
  In order to let users to review a fill the blanks attempt
  As a user
  I need to view fill in interactions in the report

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
    And the following config values are set as admin:
      # No HTML should appear even with formatstringstriptags disabled.
      | formatstringstriptags | 0 |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name           | Awesome H5P package |
      | Description    | Description         |
      | Grading method | Average grade       |
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Package file" filemanager
    And I click on "Save and display" "button"
    And I log out

  Scenario: View attempt in a fill the blanks content
    Given I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    # Do an attempt.
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Brigadoon"
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 2 of 4\")]" to "Emerald city"
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 3 of 4\")]" to "Narnia"
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 4 of 4\")]" to "Canberra"
    And I click on "Check" "button" in the ".h5p-question-buttons" "css_element"
    And I switch to the main frame
    And I reload the page
    # Check attempt.
    When I navigate to "Attempts report" in current page administration
    And I follow "View report"
    Then I should see "Of which countries are Berlin, Washington, Beijing, Canberra and Brasilia the capitals?"
    And I should see "brigadoon" in the "brasilia" "table_row"
    And "Your answer is incorrect" "icon" should exist in the "brasilia" "table_row"
    And I should see "emerald city" in the "washington" "table_row"
    And I should see "narnia" in the "berlin" "table_row"
    And "Your answer is incorrect" "icon" should exist in the "berlin" "table_row"
    And "Your answer is correct" "icon" should exist in the "canberra" "table_row"
    And I should not see "<p>"
