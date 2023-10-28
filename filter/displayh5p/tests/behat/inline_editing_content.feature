@editor @editor_atto @atto @atto_h5p @filter @filter_displayh5p @core_h5p @_file_upload @_switch_iframe
Feature: Inline editing H5P content anywhere
  In order to edit an existing H5P content
  As a user
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
      | teacher2 | C1 | teacher |
      | student1 | C1 | student        |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname        | filepath                                  |
      | Course       | C1        | contenttype_h5p | admin    | Greeting card      | /h5p/tests/fixtures/greeting-card.h5p     |
    And the following "activities" exist:
      | activity | name       | intro      | introformat | course | content  | contentformat | idnumber |
      | page     | PageName1  | PageDesc1  | 1           | C1     | H5Ptest  | 1             | 1        |
    And the "displayh5p" filter is "on"
    # Override this capability to let teachers and students to Turn editing on.
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/course:update       | Allow      | teacher        | System       |           |
      | moodle/course:update       | Allow      | student        | System       |           |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |

  @javascript @mod @mod_page
  Scenario: Edit H5P content from a page using link to private file
    Given the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/h5p:updatelibraries | Allow      | editingteacher | System       |           |
    And I log in as "teacher1"
    # Upload the H5P to private user files.
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/greeting-card.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    # Add H5P content to the page.
    And I am on "Course 1" course homepage
    And I am on the "PageName1" "page activity" page
    And I navigate to "Settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I select "Private files" repository in file picker
    And I click on "greeting-card.h5p" "file" in repository content area
    And I click on "Link to the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I click on "Save and display" "button"
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I switch to the main frame
    # The Edit button is only displayed when editing mode is on.
    And I should not see "Edit H5P content"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "PageName1" "page activity" page
    Then I should see "Edit H5P content"
    And I log out
    # Check admin can't see the Edit button (it's a private file and only the author can edit it).
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"
    And I log out
    # Check teacher2 (non-editing teacher) can't see the Edit button.
    And I log in as "teacher2"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I log in as "student1"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"

  @javascript @mod @mod_page @repository_contentbank
  Scenario: Edit H5P content from a page using link to content bank file
    Given I am on the "C1" "Course" page logged in as "admin"
    # Add H5P content to the page.
    And I am on the "PageName1" "page activity" page
    And I navigate to "Settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I select "Content bank" repository in file picker
    And I click on "Greeting card" "file" in repository content area
    And I click on "Link to the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I click on "Save and display" "button"
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I switch to the main frame
    # The Edit button is only displayed when editing mode is on.
    And I should not see "Edit H5P content"
    When I am on "Course 1" course homepage with editing mode on
    And I am on the "PageName1" "page activity" page
    Then I should see "Edit H5P content"
    And I log out
    # Check teacher1 can see the Edit button too.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"
    And I log out
    # Check teacher2 (non-editing teacher) can't see the Edit button.
    And I log in as "teacher2"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I log in as "student1"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"

  @javascript @mod @mod_page @repository_contentbank
  Scenario: Edit H5P content from a page using copy to content bank file
    Given I am on the "C1" "Course" page logged in as "admin"
    # Add H5P content to the page.
    And I am on the "PageName1" "page activity" page
    And I navigate to "Settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I select "Content bank" repository in file picker
    And I click on "Greeting card" "file" in repository content area
    And I click on "Make a copy of the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I click on "Save and display" "button"
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I switch to the main frame
    # The Edit button is only displayed when editing mode is on.
    And I should not see "Edit H5P content"
    When I am on "Course 1" course homepage with editing mode on
    And I am on the "PageName1" "page activity" page
    Then I should see "Edit H5P content"
    And I log out
    # Check teacher1 can see the Edit button too.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "PageName1" "page activity" page
    And I should see "Edit H5P content"
    And I log out
    # Check teacher2 (non-editing teacher) can't see the Edit button.
    And I log in as "teacher2"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I log in as "student1"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"

  @javascript @mod @mod_page
  Scenario: Edit H5P content from a page using external URL
    Given the following config values are set as admin:
      | allowedsources | https://moodle.h5p.com/content/[id] | filter_displayh5p |
    And I am on the "C1" "Course" page logged in as "admin"
    # Add H5P content to the page.
    And I am on the "PageName1" "page activity" page
    And I navigate to "Settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I set the field with xpath "//input[@data-region='h5pfile']" to "https://moodle.h5p.com/content/1290772960722742119"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I click on "Save and display" "button"
    And ".h5p-placeholder" "css_element" should exist
    And I switch to "h5pcontent" iframe
    And I should see "Lorum ipsum"
    And I switch to the main frame
    # The Edit button is never displayed (because it's not a local file).
    And I should not see "Edit H5P content"
    When I am on "Course 1" course homepage with editing mode on
    And I am on the "PageName1" "page activity" page
    Then I should not see "Edit H5P content"
    And I log out
    # Check teacher1 can't see the Edit button.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"
    And I log out
    # Check teacher2 (non-editing teacher) can't see the Edit button.
    And I log in as "teacher2"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I log in as "student1"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I am on the "PageName1" "page activity" page
    And I should not see "Edit H5P content"

  @javascript @block @block_html @core_block  @repository_contentbank
  Scenario: Edit H5P content from a block using copy to content bank file
    Given I am on the "C1" "Course" page logged in as "admin"
    # Add H5P content to the block.
    And I turn editing mode on
    And I add the "Text" block
    And I configure the "(new text block)" block
    And I click on "Insert H5P" "button" in the "#fitem_id_config_text" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I select "Content bank" repository in file picker
    And I click on "Greeting card" "file" in repository content area
    And I click on "Make a copy of the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I press "Save changes"
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I switch to the main frame
    # The Edit button is only displayed when editing mode is on.
    And I should see "Edit H5P content"
    When I turn editing mode off
    Then I should not see "Edit H5P content"
    And I log out
    # Check teacher1 can see the Edit button too.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I should see "Edit H5P content"
    And I log out
    # Check teacher2 (non-editing teacher) can't see the Edit button.
    And I log in as "teacher2"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I should not see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I log in as "student1"
    And I turn editing mode on
    And I am on "Course 1" course homepage
    And I should not see "Edit H5P content"
