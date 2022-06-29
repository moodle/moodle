@core @core_contentbank @core_h5p @contentbank_h5p @_file_upload @javascript
Feature: Navigate to different contexts in the content bank
  In order to navigate easily in the content bank
  I need to be able to view dropdown with all allowed contexts in the content bank

  Background:
    Given I log in as "admin"
    And the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
      | Cat 2 | 0        | CAT2     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 0 | C0        |          |
      | Course 1 | C1        | CAT1     |
      | Course 2 | C2        | CAT2     |
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname          | filepath                                |
      | System       |           | contenttype_h5p | admin    | santjordi.h5p        | /h5p/tests/fixtures/filltheblanks.h5p   |
      | Category     | CAT1      | contenttype_h5p | admin    | santjordi_rose.h5p   | /h5p/tests/fixtures/filltheblanks.h5p   |
      | Category     | CAT2      | contenttype_h5p | admin    | SantJordi_book       | /h5p/tests/fixtures/filltheblanks.h5p   |
      | Course       | C0        | contenttype_h5p | admin    | Dragon.h5p | /h5p/tests/fixtures/filltheblanks.h5p   |
      | Course       | C1        | contenttype_h5p | admin    | princess.h5p         | /h5p/tests/fixtures/filltheblanks.h5p   |
      | Course       | C2        | contenttype_h5p | admin    | mathsbook.h5p        | /h5p/tests/fixtures/filltheblanks.h5p   |

  Scenario: Admins can view and navigate to all the contexts in the content bank
    Given I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    When I click on "Content bank" "link"
    And "contextid" "select" should exist
    And the "contextid" select box should contain "System"
    And the "contextid" select box should contain "Cat 1"
    And the "contextid" select box should contain "Cat 2"
    And the "contextid" select box should contain "C0"
    And the "contextid" select box should contain "C1"
    And the "contextid" select box should contain "C2"
    And I should see "santjordi.h5p"
    And I should not see "santjordi_rose.h5p"
    And I should not see "Dragon.h5p"
    And I click on "contextid" "select"
    And I click on "Cat 1" "option"
    Then I should not see "santjordi.h5p"
    And I should see "santjordi_rose.h5p"
    And I should not see "Dragon.h5p"
    And I click on "contextid" "select"
    And I click on "C0" "option"
    And I should not see "santjordi.h5p"
    And I should not see "santjordi_rose.h5p"
    And I should see "Dragon.h5p"

  Scenario: Teachers can view and navigate to contexts in the content bank based on their permissions
    Given the following "users" exist:
      | username  | firstname | lastname |
      | teacher  | Joseba    | Cilarte  |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C0     | editingteacher |
      | teacher | C1     | editingteacher |
    And I log out
    And I am on the "C0" "Course" page logged in as "teacher"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    When I click on "Content bank" "link"
    And "contextid" "select" should exist
    And the "contextid" select box should contain "C0"
    And the "contextid" select box should contain "C1"
    And the "contextid" select box should not contain "System"
    And the "contextid" select box should not contain "Cat 1"
    And the "contextid" select box should not contain "Cat 2"
    And the "contextid" select box should not contain "C2"
    And I should see "Dragon.h5p"
    And I should not see "princess.h5p"
    And I should not see "santjordi.h5p"
    And I should not see "santjordi_rose.h5p"
    And I click on "contextid" "select"
    And I click on "C1" "option"
    Then I should not see "Dragon.h5p"
    And I should see "princess.h5p"
    And I should not see "santjordi.h5p"
    And I should not see "santjordi_rose.h5p"
    And the following "role assigns" exist:
      | user    | role          | contextlevel | reference |
      | teacher | manager       | Category     | CAT1      |
    And I am on the "C0" "Course" page logged in as "teacher"
    And I expand "Site pages" node
    When I click on "Content bank" "link"
    And "contextid" "select" should exist
    And the "contextid" select box should contain "C0"
    And the "contextid" select box should contain "C1"
    And the "contextid" select box should contain "Cat 1"
    And the "contextid" select box should not contain "System"
    And the "contextid" select box should not contain "Cat 2"
    And the "contextid" select box should not contain "C2"
    And I should see "Dragon.h5p"
    And I click on "contextid" "select"
    And I click on "Cat 1" "option"
    And I should not see "Dragon.h5p"
    And I should see "santjordi_rose.h5p"
