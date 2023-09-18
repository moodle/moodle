@core @core_contentbank @core_h5p @contentbank_h5p @_file_upload @javascript
Feature: Copy content from the content bank
  In order copy content from the content bank
  As an admin
  I need to be able to copy any content from the content bank

  Background:
    Given the following "contentbank content" exist:
      | contextlevel | reference | contenttype       | user    | contentname      | filepath                                |
      | System       |           | contenttype_h5p   | admin   | content2copy.h5p | /h5p/tests/fixtures/filltheblanks.h5p   |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"

  Scenario: Admins can copy content from the content bank
    Given I log in as "admin"
    And I am on site homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link"
    And I click on "content2copy.h5p" "link"
    And I click on "More" "button"
    And I click on "Copy content" "link" in the ".cb-toolbar-container" "css_element"
    And I set the following fields to these values:
      | Content name | |
    And I click on "Save changes" "button"
    Then I should see "Empty name is not allowed"
    And I click on "OK" "button" in the "Error" "dialogue"
    And I set the following fields to these values:
      | Content name | Fill the blanks copy 1 |
    And I click on "Save changes" "button"
    Then I should see "Fill the blanks copy 1"
    And I click on "Edit" "link"
    And I switch to "h5p-editor-iframe" class iframe
    Then the field "Title" matches value "Geography"

  Scenario: Users without the required capability cannot copy content
    Given the following "users" exist:
      | username    | firstname | lastname | email              |
      | manager     | Max       | Manager  | man@example.com    |
    And the following "role assigns" exist:
      | user        | role      | contextlevel  | reference     |
      | manager     | manager   | System        |               |
    And the following "permission overrides" exist:
      | capability                         | permission | role    | contextlevel | reference |
      | moodle/contentbank:copycontent     | Prohibit   | manager | System       |           |
      | moodle/contentbank:copyanycontent  | Prohibit   | manager | System       |           |
    And I log out
    And I log in as "manager"
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I should see "content2copy.h5p"
    And I follow "content2copy.h5p"
    And  I click on "More" "button"
    Then I should not see "Copy content"

  Scenario: Users can't copy content if they don't have the required permission
    Given I log in as "admin"
    And I am on site homepage
    Given I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | teacher1 | Teacher   | 1        | user1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname       | filepath                              |
      | Course       | C1        | contenttype_h5p | teacher1 | filltheblanks.h5p | /h5p/tests/fixtures/filltheblanks.h5p |
    When I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I follow "filltheblanks.h5p"
    And  I click on "More" "button"
    Then I should see "Copy content"
    And the following "permission overrides" exist:
      | capability                     | permission | role           | contextlevel | reference |
      | moodle/contentbank:copycontent | Prohibit   | editingteacher | System       |           |
    And I reload the page
    And  I click on "More" "button"
    Then I should not see "Copy content"

  Scenario: Teachers can copy their own content in the content bank
    Given I log in as "admin"
    And I am on site homepage
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
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I follow "ipsums.h5p"
    And  I click on "More" "button"
    Then I should see "Copy content"

  Scenario: Teachers can't copy content created by other users in the content bank
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
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I follow "filltheblanks.h5p"
    And  I click on "More" "button"
    Then I should not see "Copy content"

  Scenario: Teachers can copy any content created by other users in the content bank if allowed
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
    And the following "permission overrides" exist:
      | capability                        | permission | role           | contextlevel | reference |
      | moodle/contentbank:copyanycontent | Allow      | editingteacher | System       |           |
    When I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link"
    And I follow "filltheblanks.h5p"
    And  I click on "More" "button"
    Then I should see "Copy content"
