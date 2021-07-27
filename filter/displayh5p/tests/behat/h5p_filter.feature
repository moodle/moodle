@editor @filter @filter_displayh5p @core_h5p @_file_upload @_switch_iframe
Feature: Render H5P content using filters
  To write rich text - I need to render H5P content.

  Background:
    Given the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student        |
    And the following "activities" exist:
      | activity | name       | intro      | introformat | course | content  | contentformat | idnumber |
      | page     | PageName1  | PageDesc1  | 1           | C1     | H5Ptest  | 1             | 1        |
      | page     | PageName2  | PageDesc1  | 1           | C1     | H5Ptest  | 1             | 1        |
    And the "displayh5p" filter is "on"
    And the following config values are set as admin:
      | allowedsources | https://moodle.h5p.com/content/[id]/embed | filter_displayh5p |

  @javascript @external
  Scenario: Render an external H5P content URL.
    Given I am on the PageName1 "page activity editing" page logged in as teacher1
    And I set the field "Page content" to "<div>Go for it</div>https://moodle.h5p.com/content/1290772960722742119/embed"
    When I click on "Save and display" "button"
    And I wait until the page is ready
    And I switch to "h5p-iframe" class iframe
    Then I should see "Lorum ipsum"

  @javascript
  Scenario: Add an external H5P content URL in a link with the URL. Should be rendered.
    Given I am on the PageName1 "page activity editing" page logged in as teacher1
#   This content won't be displayed, so this scenario shouldn't be labeled as external.
    And I set the field "Page content" to "<a href='https://moodle.h5p.com/content/1290772960722742119/embed'>https://moodle.h5p.com/content/1290772960722742119/embed</a>"
    When I click on "Save and display" "button"
    And I wait until the page is ready
    And I switch to "h5p-iframe" class iframe
    Then I should see "Lorum ipsum"

  Scenario: Add an external H5P content URL in a link with text. Shouldn't be rendered.
    Given I am on the PageName1 "page activity editing" page logged in as teacher1
#   This content won't be displayed, so this scenario shouldn't be labeled as external.
    And I set the field "Page content" to "<a href='https://moodle.h5p.com/content/1290772960722742119/embed'>Here you are the content</a>"
    When I click on "Save and display" "button"
    And I wait until the page is ready
    Then ".h5p-iframe" "css_element" should not exist

  @javascript
  Scenario: Render a local H5P file as admin
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "File" to section "1"
    And I set the following fields to these values:
      | Name                      | ipsumFile     |
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "Select files" filemanager
    And I press "Save and return to course"
    And I am on the PageName1 "page activity editing" page
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I click on "Server files" "link" in the ".fp-repo-area" "css_element"
    And I click on "ipsumFile (File)" "link"
    And I click on "ipsums.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    When I click on "Save and display" "button"
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page
    And I switch to "h5p-iframe" class iframe
    Then I should see "Lorum ipsum"
    And I switch to the main frame
    And I log out
    And I am on the PageName1 "page activity" page logged in as student1
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page
    And I switch to "h5p-iframe" class iframe
    And I should not see "you don't have access"
    And I should see "Lorum ipsum"

  @javascript
  Scenario: Render a local H5P file as teacher
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "File" to section "1"
    And I set the following fields to these values:
      | Name                      | ipsumFile     |
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "Select files" filemanager
    And I press "Save and return to course"
    And I am on the PageName1 "page activity editing" page
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I click on "Server files" "link" in the ".fp-repo-area" "css_element"
    And I click on "ipsumFile (File)" "link"
    And I click on "ipsums.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    When I click on "Save and display" "button"
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
    Then I should see "Note that the libraries may exist in the file you uploaded, but you're not allowed to upload new libraries."
    And I should see "missing-required-library"

  @javascript
  Scenario: Render a local H5P file with existing libraries
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "File" to section "1"
    And I set the following fields to these values:
      | Name                      | ipsumFileTeacher     |
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "Select files" filemanager
    And I press "Save and return to course"
    And I am on the PageName1 "page activity editing" page
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I click on "Server files" "link" in the ".fp-repo-area" "css_element"
    And I click on "ipsumFileTeacher (File)" "link"
    And I click on "ipsums.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    And I click on "Save and display" "button"
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Libraries don't exist, so an error should be displayed.
    And I should see "missing-required-library"
    And I switch to the main frame
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "File" to section "1"
    And I set the following fields to these values:
      | Name                      | ipsumFile     |
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "Select files" filemanager
    And I press "Save and return to course"
    And I am on the PageName2 "page activity editing" page
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I click on "Server files" "link" in the ".fp-repo-area" "css_element"
    And I click on "ipsumFile (File)" "link"
    And I click on "ipsums.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    And I click on "Save and display" "button"
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page
    And I switch to "h5p-iframe" class iframe
#   Libraries have been installed.
    And I should see "Lorum ipsum"
    And I switch to the main frame
    And I log out
    And I am on the PageName1 "page activity" page logged in as teacher1
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page
    And I switch to "h5p-iframe" class iframe
    Then I should not see "missing-required-library"
    And I should see "Lorum ipsum"

  @javascript
  Scenario: Render local H5P file with a disabled main library
    Given I log in as "admin"
# Upload H5P file to private files.
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
# Upload manually the H5P content-type library and disable it.
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I click on "Disable" "link" in the "Accordion" "table_row"
# Add H5P content to the page.
    And I am on "Course 1" course homepage
    And I am on the PageName1 "page activity editing" page
    When I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "ipsums.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I click on "Save and display" "button"
    And I switch to "h5p-iframe" class iframe
#   Library is disabled, so an error should be displayed.
    Then I should see "This file can't be displayed because its content type is disabled."
    And I should not see "Lorum ipsum"
    And I switch to the main frame
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I click on "Enable" "link" in the "Accordion" "table_row"
#   Content should be deployed now that main library is enabled.
    And I am on the PageName1 "page activity" page
#   Switch to iframe created by filter.
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page.
    And I switch to "h5p-iframe" class iframe
    And I should see "Lorum ipsum"
    And I should not see "This file can't be displayed because its content type is disabled."
    And I switch to the main frame
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I click on "Disable" "link" in the "Accordion" "table_row"
#   Library is disabled again, so an error should be displayed.
    And I am on the PageName1 "page activity" page
    And I switch to "h5p-iframe" class iframe
    And I should see "This file can't be displayed because its content type is disabled."
    And I should not see "Lorum ipsum"
    And I switch to the main frame
