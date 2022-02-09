@core @core_contentbank @core_h5p @contenttype_h5p @_file_upload @javascript
Feature: Manage H5P content from the content bank
  In order to manage H5P content in the content bank
  As an admin
  I need to be able to edit any H5P content in the content bank

  Background:
    Given I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
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
      | contextlevel | reference | contenttype     | user     | contentname       | filepath                                |
      | Course       | C1        | contenttype_h5p | admin    | filltheblanks.h5p | /h5p/tests/fixtures/filltheblanks.h5p   |
      | Course       | C1        | contenttype_h5p | teacher1 | ipsums.h5p        | /h5p/tests/fixtures/ipsums.h5p          |
    And I am on "Course 1" course homepage with editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I log out

  Scenario: Teachers can rename their own content in the content bank
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I follow "ipsums.h5p"
    When I click on "More" "button"
    And I should see "Rename"
    And I click on "Rename" "link"
    And I set the field "Content name" to "New name"
    And I click on "Rename" "button"
    And I wait until the page is ready
    Then I should not see "ipsums.h5p"
    And I should see "New name"

  Scenario: Teachers can't rename content created by other users in the content bank
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    When I follow "filltheblanks.h5p"
    And I click on "More" "button"
    Then I should not see "Rename"
