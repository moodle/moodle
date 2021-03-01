@core @core_contentbank @core_h5p @contentbank_h5p @_file_upload @javascript
Feature: Content bank use editor feature
  In order to add/edit content
  As a user
  I need to be able to access the edition options

  Background:
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"

  Scenario: Users see the Add button disabled if there is no content type available for creation
    Given I click on "Site pages" "list_item" in the "Navigation" "block"
    When I click on "Content bank" "link"
    Then the "[data-action=Add-content]" "css_element" should be disabled

  Scenario: Users can see the Add button if there is content type available for creation
    Given I follow "Dashboard" in the user menu
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    When I click on "Content bank" "link"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Close" "link"
    Then I click on "[data-action=Add-content]" "css_element"
    And I should see "Fill in the Blanks"

  Scenario: Users can edit content if they have the required permission
    Given I follow "Dashboard" in the user menu
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    When I click on "Content bank" "link"
    And I click on "filltheblanks.h5p" "link"
    Then I click on "Edit" "link"
    And I switch to "h5p-editor-iframe" class iframe
    And I switch to the main frame
    And I click on "Cancel" "button"
    And I should see "filltheblanks.h5p" in the "h1" "css_element"

  Scenario: Users can create new content if they have the required permission
    Given I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I should see "H5P content types uploaded successfully"
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    When I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "[data-action=Add-content]" "css_element"
    Then I click on "Fill in the Blanks" "link"
    And I switch to "h5p-editor-iframe" class iframe
    And I switch to the main frame
    And I click on "Cancel" "button"

  Scenario: Users can't edit content if they don't have the required permission
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | teacher1 | Teacher   | 1        | user1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role              |
      | teacher1 | C1     | editingteacher    |
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I should see "H5P content types uploaded successfully"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link"
    And "[data-action=Add-content]" "css_element" should exist
    When the following "permission overrides" exist:
      | capability                       | permission | role           | contextlevel | reference |
      | moodle/contentbank:useeditor     | Prohibit   | editingteacher | System       |           |
    And I reload the page
    Then "[data-action=Add-content]" "css_element" should not exist

  Scenario: Users can edit content and save changes
    Given the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user  | contentname             | filepath                                    |
      | System       |           | contenttype_h5p | admin | filltheblanks.h5p       | /h5p/tests/fixtures/filltheblanks.h5p       |
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Edit" "link"
    And I switch to "h5p-editor-iframe" class iframe
    And the field "Title" matches value "Geography"
    And I set the field "Title" to "New title"
    And I switch to the main frame
    When I click on "Save" "button"
    And I should see "filltheblanks.h5p" in the "h1" "css_element"
    And I click on "Edit" "link"
    And I switch to "h5p-editor-iframe" class iframe
    Then the field "Title" matches value "New title"

  Scenario: Teachers can edit their own content in the content bank
    Given I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname       | filepath                              |
      | Course       | C1        | contenttype_h5p | admin    | filltheblanks.h5p | /h5p/tests/fixtures/filltheblanks.h5p |
      | Course       | C1        | contenttype_h5p | teacher1 | ipsums.h5p        | /h5p/tests/fixtures/ipsums.h5p        |
    When I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I follow "ipsums.h5p"
    Then "Edit" "link" should exist in the "region-main" "region"

  Scenario: Teachers can't edit content created by other users in the content bank
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname       | filepath                              |
      | Course       | C1        | contenttype_h5p | admin    | filltheblanks.h5p | /h5p/tests/fixtures/filltheblanks.h5p |
      | Course       | C1        | contenttype_h5p | teacher1 | ipsums.h5p        | /h5p/tests/fixtures/ipsums.h5p        |
    When I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I follow "filltheblanks.h5p"
    Then "Edit" "link" should not exist in the "region-main" "region"

  Scenario: Teachers keep their content authoring in copied courses
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname       | filepath                              |
      | Course       | C1        | contenttype_h5p | admin    | filltheblanks.h5p | /h5p/tests/fixtures/filltheblanks.h5p |
      | Course       | C1        | contenttype_h5p | teacher1 | ipsums.h5p        | /h5p/tests/fixtures/ipsums.h5p        |
    And I am on "Course 1" course homepage
    And I navigate to "Copy course" in current page administration
    And I set the following fields to these values:
      | Course full name  | Copy |
      | Course short name | Copy |
      | Teacher           | 1    |
    When I press "Copy and view"
    And I trigger cron
    And I am on homepage
    And I log out
    And I log in as "teacher1"
    And I am on "Copy" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I follow "ipsums.h5p"
    Then "Edit" "link" should exist in the "region-main" "region"
    And I click on "Content bank" "link"
    And I follow "filltheblanks.h5p"
    Then "Edit" "link" should not exist in the "region-main" "region"
