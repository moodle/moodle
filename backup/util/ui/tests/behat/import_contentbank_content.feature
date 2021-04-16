@core @core_backup @core_contentbank @core_h5p @contenttype_h5p @_file_upload @javascript
Feature: Import course content bank content
  In order to import content from a course contentbank
  As a teacher
  I need to confirm that errors will not happen

  Background:
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname | filepath                        |
      | Course       | C1        | contenttype_h5p | teacher1 | ipsums.h5p  | /h5p/tests/fixtures/ipsums.h5p  |
    And I log out
    And I log in as "teacher1"

  Scenario: Import content bank content to another course
    Given I am on "Course 2" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should not see "ipsums.h5p"
    When I import "Course 1" course into "Course 2" course using this options:
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    Then I should see "ipsums.h5p"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should see "ipsums.h5p"

  Scenario: User could configure not to import content bank
    Given I am on "Course 2" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should not see "ipsums.h5p"
    When I import "Course 1" course into "Course 2" course using this options:
      | Initial | Include content bank content | 0 |
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    Then I should not see "ipsums.h5p"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should see "ipsums.h5p"
