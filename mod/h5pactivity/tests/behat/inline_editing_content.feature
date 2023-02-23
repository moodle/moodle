@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe
Feature: Inline editing H5P content
  In order to edit an existing H5P activity file
  As a teacher
  I need to see the button and access to the H5P editor

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
      | student1  | Student  | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | editingteacher |
      | student1 | C1 | student        |
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/h5p:updatelibraries | Allow      | editingteacher | System       |           |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |

  @javascript
  Scenario: Add H5P activity using link to content bank file
    Given the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user        | contentname        | filepath                                  |
      | Course       | C1        | contenttype_h5p | teacher1    | Greeting card      | /h5p/tests/fixtures/greeting-card-887.h5p |
    And I log in as "admin"
    # Add the navigation block.
    And I am on "Course 1" course homepage with editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    # Create an H5P activity with a link to the content-bank file.
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name                       | H5P package added as link to content bank |
      | Description                | Description                                 |
    And I click on "Add..." "button" in the "Package file" "form_row"
    And I select "Content bank" repository in file picker
    And I click on "Greeting card" "file" in repository content area
    And I click on "Link to the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Save and display" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I switch to the main frame
    # Modify the H5P content using the edit button (which opens the H5P editor).
    And I follow "Edit H5P content"
    And I should see "This content may be in use in other places."
    And I switch to "h5p-editor-iframe" class iframe
    And I set the field "Greeting text" to "It's a Wonderful Life!"
    And I switch to the main frame
    And I click on "Save changes" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    # Check the H5P content has changed.
    And I should not see "Hello world!"
    And I should see "It's a Wonderful Life!"
    And I switch to the main frame
    # Check the H5P has also changed into the content bank.
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "Greeting card" "link"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should not see "Hello world!"
    And I should see "It's a Wonderful Life!"
    And I switch to the main frame
    And I log out
    # Check teacher1 can see the Edit button (because she is the author of this file in the content bank).
    And I am on the "H5P package added as link to content bank" "h5pactivity activity" page logged in as teacher1
    And I should see "Edit H5P content"
    And I log out
    # Check teacher2 can't see the Edit button (because the file was created by the teacher1).
    When I am on the "H5P package added as link to content bank" "h5pactivity activity" page logged in as teacher2
    Then I should not see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I am on the "H5P package added as link to content bank" "h5pactivity activity" page logged in as student1
    And I should not see "Edit H5P content"

  @javascript
  Scenario: Add H5P activity using copy to content bank file
    Given the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname        | filepath                                  |
      | Course       | C1        | contenttype_h5p | admin    | Greeting card      | /h5p/tests/fixtures/greeting-card-887.h5p |
    And I log in as "admin"
    # Add the navigation block.
    And I am on "Course 1" course homepage with editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    # Create an H5P activity with a copy to the content-bank file.
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name                       | H5P package added as copy to content bank |
      | Description                | Description                                 |
    And I click on "Add..." "button" in the "Package file" "form_row"
    And I select "Content bank" repository in file picker
    And I click on "Greeting card" "file" in repository content area
    And I click on "Make a copy of the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Save and display" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I switch to the main frame
    # Modify the H5P content using the edit button (which opens the H5P editor).
    And I follow "Edit H5P content"
    And I should not see "This content may be in use in other places."
    And I switch to "h5p-editor-iframe" class iframe
    And I set the field "Greeting text" to "The nightmare before Christmas"
    And I switch to the main frame
    And I click on "Save changes" "button"
    # Check the H5P content has changed.
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should not see "Hello world!"
    And I should see "The nightmare before Christmas"
    And I switch to the main frame
    # Check the H5P has also changed into the content bank.
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "Greeting card" "link"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I should not see "The nightmare before Christmas"
    And I switch to the main frame
    And I log out
    # Check teacher1 can see the Edit button (because the file is a copy).
    And I am on the "H5P package added as copy to content bank" "h5pactivity activity" page logged in as teacher1
    And I should see "Edit H5P content"
    And I log out
    # Check teacher2 can also see the Edit button (because the file is a copy).
    When I am on the "H5P package added as copy to content bank" "h5pactivity activity" page logged in as teacher2
    Then I should see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I am on the "H5P package added as copy to content bank" "h5pactivity activity" page logged in as student1
    And I should not see "Edit H5P content"

  @javascript
  Scenario: Add H5P activity using private user file
    Given I log in as "teacher1"
    # Upload the H5P to private user files.
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/greeting-card-887.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    # Create an H5P activity with a private user file.
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name                       | H5P package added as private user file      |
      | Description                | Description                                 |
    And I click on "Add..." "button" in the "Package file" "form_row"
    And I select "Private files" repository in file picker
    And I click on "greeting-card-887.h5p" "file" in repository content area
    And I click on "Link to the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Save and display" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I switch to the main frame
    # Modify the H5P content using the edit button (which opens the H5P editor).
    And I follow "Edit H5P content"
    And I should see "This content may be in use in other places."
    And I switch to "h5p-editor-iframe" class iframe
    And I set the field "Greeting text" to "Little women"
    And I switch to the main frame
    And I click on "Save changes" "button"
    # Check the H5P content has changed.
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should not see "Hello world!"
    And I should see "Little women"
    And I switch to the main frame
    And I log out
    # Check admin can't see the Edit button (because the file belongs to teacher1).
    And I am on the "H5P package added as private user file" "h5pactivity activity" page logged in as admin
    And I should not see "Edit H5P content"
    And I log out
    # Check teacher2 can't see the Edit button (because the file belongs to teacher1).
    When I am on the "H5P package added as private user file" "h5pactivity activity" page logged in as teacher2
    Then I should not see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I am on the "H5P package added as private user file" "h5pactivity activity" page logged in as student1
    And I should not see "Edit H5P content"
