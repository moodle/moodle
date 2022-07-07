@editor @editor_atto @atto @atto_h5p @core_h5p @_file_upload @_switch_iframe
Feature: Add h5ps to Atto
  To write rich text - I need to add h5ps.

  Background:
    Given the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name       | intro      | introformat | course | content  | contentformat | idnumber |
      | page     | PageName1  | PageDesc1  | 1           | C1     | H5Ptest  | 1             | 1        |
    And the "displayh5p" filter is "on"
    And the following config values are set as admin:
      | allowedsources | https://moodle.h5p.com/content/[id] | filter_displayh5p |

  @javascript @external
  Scenario: Insert an embedded h5p
    Given I log in as "admin"
    And I change window size to "large"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    And I navigate to "Edit settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I set the field with xpath "//input[@data-region='h5pfile']" to "https://moodle.h5p.com/content/1290772960722742119"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    When I click on "Save and display" "button"
    Then ".h5p-placeholder" "css_element" should exist
    And I wait until the page is ready
    And I switch to "h5pcontent" iframe
    And I should see "Lorum ipsum"

  @javascript
  Scenario: Insert an h5p file
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/guess-the-answer.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    And I navigate to "Edit settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "guess-the-answer.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    When I click on "Save and display" "button"
    Then ".h5p-placeholder" "css_element" should exist

  @javascript
  Scenario: Test an invalid url
    Given I log in as "admin"
    And I change window size to "large"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    And I navigate to "Edit settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
#   This is not a real external URL, so this scenario shouldn't be labeled as external.
    And I set the field with xpath "//input[@data-region='h5pfile']" to "ftp://moodle.h5p.com/content/1290772960722742119"
    When I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    Then I should see "Invalid URL" in the "Insert H5P" "dialogue"

  @javascript
  Scenario: No h5p capabilities
    Given the following "permission overrides" exist:
    | capability | permission | role | contextlevel | reference |
    | atto/h5p:addembed | Prohibit | editingteacher | Course | C1 |
    | moodle/h5p:deploy | Prohibit | editingteacher | Course | C1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    When I navigate to "Edit settings" in current page administration
    Then "Insert H5P" "button" should not exist

  @javascript
  Scenario: No embed h5p capabilities
    Given the following "permission overrides" exist:
    | capability | permission | role | contextlevel | reference |
    | atto/h5p:addembed | Prohibit | editingteacher | Course | C1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    When I navigate to "Edit settings" in current page administration
    And I click on "Insert H5P" "button"
    Then I should not see "H5P URL" in the "Insert H5P" "dialogue"
    And I should see "H5P file upload" in the "Insert H5P" "dialogue"
    And I should see "H5P options" in the "Insert H5P" "dialogue"

  @javascript
  Scenario: No upload h5p capabilities
    Given the following "permission overrides" exist:
    | capability | permission | role | contextlevel | reference |
    | moodle/h5p:deploy | Prohibit | editingteacher | Course | C1 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    When I navigate to "Edit settings" in current page administration
    And I click on "Insert H5P" "button"
    Then I should not see "H5P file upload" in the "Insert H5P" "dialogue"
    And I should see "H5P URL" in the "Insert H5P" "dialogue"
    And I should not see "H5P options" in the "Insert H5P" "dialogue"

  @javascript @external
  Scenario: Edit H5P content
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "lib/editor/atto/tests/fixtures/drag.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    And I navigate to "Edit settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
#   H5P file content
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "drag" "link"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    When I click on "Save and display" "button"
    And I switch to "h5pcontent" iframe
    And I switch to "h5p-iframe" class iframe
    Then I should not see "reveal"
    And I should see "Cloudberries"
    And I switch to the main frame
    And I navigate to "Edit settings" in current page administration
    And I click on ".h5p-placeholder" "css_element"
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
#   External URL
    And I set the field with xpath "//input[@data-region='h5pfile']" to "https://moodle.h5p.com/content/1290772960722742119"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    And I click on "Save and display" "button"
    And I wait until the page is ready
    And I switch to "h5pcontent" iframe
    And I should see "Lorum ipsum"
    And I should not see "Cloudberries"

  @javascript
  Scenario: Enable/disable H5P options
    Given I log in as "admin"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/guess-the-answer.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    And I navigate to "Edit settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "guess-the-answer.h5p" "link"
    And I click on "Select this file" "button"
#   No display option button displayed
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    When I click on "Save and display" "button"
    And I wait until the page is ready
    And I switch to "h5pcontent" iframe
    And I switch to "h5p-iframe" class iframe
    Then I should not see "Reuse"
    And I should not see "Embed"
    And I should not see "Rights of use"
    And I switch to the main frame
    And I navigate to "Edit settings" in current page administration
    And I click on ".h5p-placeholder" "css_element"
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "H5P options" "link"
#   Only Allow Download button displayed
    And I click on "Allow download" "checkbox"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    And I click on "Save and display" "button"
    And I wait until the page is ready
    And I switch to "h5pcontent" iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Reuse"
    And I should not see "Embed"
    And I should not see "Rights of use"
    And I switch to the main frame
    And I navigate to "Edit settings" in current page administration
    And I click on ".h5p-placeholder" "css_element"
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
#   Embed and copyright buttons displayed. Download not displayed
    And I click on "Allow download" "checkbox"
    And I click on "Embed button" "checkbox"
    And I click on "Copyright button" "checkbox"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    And I click on "Save and display" "button"
    And I wait until the page is ready
    And I switch to "h5pcontent" iframe
    And I switch to "h5p-iframe" class iframe
    And I should not see "Reuse"
    And I should see "Embed"
    And I should see "Rights of use"

  @javascript @external
  Scenario: H5P options are ignored for H5P URLs
    Given I log in as "admin"
    And I change window size to "large"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    And I navigate to "Edit settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I set the field with xpath "//input[@data-region='h5pfile']" to "https://moodle.h5p.com/content/1290752078589054689"
    And I click on "H5P options" "link"
    And I click on "Embed button" "checkbox"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    When I click on "Save and display" "button"
    Then ".h5p-placeholder" "css_element" should exist
    And I wait until the page is ready
    And I switch to "h5pcontent" iframe
    And I should see "History of strawberries"
    And I should not see "Embed"
    And I switch to the main frame
    And I navigate to "Edit settings" in current page administration
    And I click on ".h5p-placeholder" "css_element"
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "H5P options" "link"
    And "input[aria-label=\"Embed button\"]:not([checked=checked])" "css_element" should exist

  @javascript
  Scenario: Private H5P files are shown to students
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student   | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    And I log in as "admin"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/guess-the-answer.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    And I navigate to "Edit settings" in current page administration
    And I click on "Insert H5P" "button" in the "#fitem_id_page" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "guess-the-answer.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I wait until the page is ready
    And I click on "Save and display" "button"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "PageName1"
    Then I switch to "h5pcontent" iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "reveal"
