@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe @javascript
Feature: Users can save the current state of an H5P activity
  In order to continue an H5P activity where I left
  As a user
  I need to be able to save the current state

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course" exists:
      | fullname  | Course 1 |
      | shortname | C1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/h5p:updatelibraries | Allow      | editingteacher | System       |           |
    And the following "activity" exists:
      | activity        | h5pactivity                             |
      | course          | C1                                      |
      | name            | Awesome H5P package                     |
      | packagefilepath | h5p/tests/fixtures/filltheblanks.h5p    |

  Scenario: Content state is not saved when enablesavestate is disabled
    Given the following config values are set as admin:
      | enablesavestate | 0 | mod_h5pactivity|
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Narnia"
    And I switch to the main frame
    And I am on the "Course 1" course page
    When I am on the "Awesome H5P package" "h5pactivity activity" page
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" does not match value "Narnia"

  Scenario: Content state is saved when enablesavestate is enabled
    Given the following config values are set as admin:
      | enablesavestate | 1 | mod_h5pactivity|
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Narnia"
    And I switch to the main frame
    And I am on the "Course 1" course page
    When I am on the "Awesome H5P package" "h5pactivity activity" page
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" matches value "Narnia"

  Scenario: Content state is not saved for teachers when enablesavestate is enabled
    Given the following config values are set as admin:
      | enablesavestate | 1 | mod_h5pactivity|
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as teacher1
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Narnia"
    And I switch to the main frame
    And I am on the "Course 1" course page
    When I am on the "Awesome H5P package" "h5pactivity activity" page
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" does not match value "Narnia"

  Scenario: Content state is reseted when content changes
    Given the following config values are set as admin:
      | enablesavestate | 1 | mod_h5pactivity|
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Narnia"
    And I switch to the main frame
    And I am on the "Course 1" course page
    When I am on the "Awesome H5P package" "h5pactivity activity" page logged in as admin
    # Change the content.
    And I follow "Edit H5P content"
    And I switch to "h5p-editor-iframe" class iframe
    And I set the field "Title" to "Capitals"
    And I switch to the main frame
    And I click on "Save changes" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Check"
    # Check the content state has been reseted.
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then I should see "Data Reset"
    And I should see "This content has changed since you last used it."
    And I click on "OK" "button"
    And the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" does not match value "Narnia"

  Scenario: Content state is not reseted when content edition is cancelled
    Given the following config values are set as admin:
      | enablesavestate | 1 | mod_h5pactivity|
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Narnia"
    And I switch to the main frame
    And I am on the "Course 1" course page
    When I am on the "Awesome H5P package" "h5pactivity activity" page logged in as admin
    # Start content edition.
    And I follow "Edit H5P content"
    And I switch to "h5p-editor-iframe" class iframe
    And I set the field "Title" to "Capitals"
    And I switch to the main frame
    And I click on "Cancel" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Check"
    # Check the content state hasn't been reseted.
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I should see "Awesome H5P package"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then I should not see "Data Reset"
    And I should not see "This content has changed since you last used it."
    And the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" matches value "Narnia"

  Scenario: Content state is removed when an attempt is created
    Given the following config values are set as admin:
      | enablesavestate | 1 | mod_h5pactivity|
    # Save state content for student2, to check this data is not removed when student1 finishes their attempt.
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student2
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Vallhonesta"
    # Confirm the content state has been saved properly.
    And I reload the page
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" matches value "Vallhonesta"
    # Create an attempt for student1.
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student1
    And I should not see "Attempts report"
    When I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I set the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" to "Narnia"
    And I click on "Check" "button"
    # Check the state content has been removed.
    And I reload the page
    Then I should see "Attempts report"
    And I am on the "Awesome H5P package" "h5pactivity activity" page
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" does not match value "Narnia"
    And I switch to the main frame
    # Check the state content for student2 is still there.
    And I am on the "Awesome H5P package" "h5pactivity activity" page logged in as student2
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And the field with xpath "//input[contains(@aria-label,\"Blank input 1 of 4\")]" matches value "Vallhonesta"
