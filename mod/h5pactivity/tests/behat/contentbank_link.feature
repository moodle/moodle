@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe
Feature: Content bank link in the activity settings form
  In order to have direct access to the Content bank
  As a teacher
  I need to see a Content bank link in the activity settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname         | filepath                                  |
      | Course       | C1        | contenttype_h5p | admin    | filltheblanks.h5p   | /h5p/tests/fixtures/filltheblanks.h5p     |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |

  @javascript
  Scenario: The content bank link should go to the course Content bank
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    Then I should see "Use the content bank (opens in new window) to manage your H5P files"
    And I click on "content bank (opens in new window)" "link" in the "General" "fieldset"
    And I switch to the browser tab opened by the app
    And I should see "Content bank" in the "page-content" "region"
    And I should see "filltheblanks.h5p" in the "page-content" "region"
    And I close all opened windows

  @javascript
  Scenario: Content bank is not displayed if the user don't have access to the content bank
    Given the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/contentbank:access  | Prevent    | editingteacher | Course       | C1        |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    Then I should not see "Use the content Bank (opens in new window) to manage your H5P files"

  @javascript
  Scenario: A different message should be displayed if the package file is a link to the content bank file
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name                       | H5P package added with link to content bank |
      | Description                | Description                                 |
    And I click on "Add..." "button" in the "Package file" "form_row"
    And I select "Content bank" repository in file picker
    And I click on "filltheblanks.h5p" "file" in repository content area
    And I click on "Link to the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Save and display" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Of which countries are Berlin, Washington, Beijing, Canberra and Brasilia the capitals?"
    And I switch to the main frame
    When I navigate to "Settings" in current page administration
    Then I should not see "Use the content Bank (opens in new window) to manage your H5P files"
    And I should see "Access the H5P file in the content bank (opens in a new window)."
    And I follow "Access the H5P file in the content bank"

  @javascript
  Scenario: The content bank link should go to the course Content bank if the file is a copy to a content bank file
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name                       | H5P package added with link to content bank |
      | Description                | Description                                 |
    And I click on "Add..." "button" in the "Package file" "form_row"
    And I select "Content bank" repository in file picker
    And I click on "filltheblanks.h5p" "file" in repository content area
    And I click on "Make a copy of the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Save and display" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Of which countries are Berlin,"
    And I switch to the main frame
    When I navigate to "Settings" in current page administration
    Then I should see "Use the content bank (opens in new window) to manage your H5P files"

  @javascript
  Scenario: The content bank link should go to the course Content bank if the file is referenced but to another repository
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/guess-the-answer.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    And I set the following fields to these values:
      | Name                       | H5P package added with link to content bank |
      | Description                | Description                                 |
    And I click on "Add..." "button" in the "Package file" "form_row"
    And I select "Private files" repository in file picker
    And I click on "guess-the-answer.h5p" "file" in repository content area
    And I click on "Link to the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Save and display" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Which fruit is this?"
    And I switch to the main frame
    When I navigate to "Settings" in current page administration
    Then I should see "Use the content bank (opens in new window) to manage your H5P files"
