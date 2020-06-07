@core @core_contentbank @contenttype_h5p @_file_upload @javascript
Feature: Manage H5P content from the content bank
  In order to manage H5P content in the content bank
  As an admin
  I need to be able to edit any H5P content in the content bank

  Background:
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
      | contextlevel | reference | contenttype     | user     | contentname       |
      | Course       | C1        | contenttype_h5p | admin    | filltheblanks.h5p |
      | Course       | C1        | contenttype_h5p | teacher1 | ipsums.h5p        |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation" block if not present
    And I log out

  Scenario: Teachers can rename their own content in the content bank
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I follow "ipsums.h5p"
    When I open the action menu in "region-main-settings-menu" "region"
    And I should see "Rename"
    And I choose "Rename" in the open action menu
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
    Then "region-main-settings-menu" "region" should not exist
