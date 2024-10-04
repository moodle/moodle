@core @core_contentbank @core_h5p @contenttype_h5p @_file_upload @javascript
Feature: Disable H5P content-types from the content bank
  In order to disable H5P content-types
  As an admin
  I need to be able to check they are not displayed in the content bank

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
    And the following "contentbank contents" exist:
      | contextlevel | reference | contenttype     | user     | contentname       | filepath                              |
      | Course       | C1        | contenttype_h5p | admin    | filltheblanks     | /h5p/tests/fixtures/filltheblanks.h5p |
      | Course       | C1        | contenttype_h5p | admin    | accordion         | /h5p/tests/fixtures/ipsums.h5p        |
      | Course       | C1        | contenttype_h5p | admin    | invalidh5p        | /h5p/tests/fixtures/h5ptest.zip       |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I log out

  Scenario: Teachers cannot view disabled or invalid content-types
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should see "accordion"
    And I should see "filltheblanks"
    And I should not see "invalidh5p"
    And I log out
    And I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I click on "Disable" "link" in the "Accordion" "table_row"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    Then I should not see "accordion"
    And I should see "filltheblanks"
    And I should not see "invalidh5p"

  Scenario: Admins cannot view disabled content-types
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should see "accordion"
    And I should see "filltheblanks"
    And I should see "invalidh5p"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I click on "Disable" "link" in the "Accordion" "table_row"
    When I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    Then I should not see "accordion"
    And I should see "filltheblanks"
    And I should see "invalidh5p"

  Scenario: Teachers cannot create disabled content-types
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "[data-action=Add-content]" "css_element"
    And I should see "Accordion"
    And I should see "Fill in the Blanks"
    And I log out
    And I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I click on "Disable" "link" in the "Accordion" "table_row"
    And I log out
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "[data-action=Add-content]" "css_element"
    Then I should not see "Accordion"
    And I should see "Fill in the Blanks"

  Scenario: Admins cannot create disabled content-types
    Given I log in as "admin"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "[data-action=Add-content]" "css_element"
    And I should see "Accordion"
    And I should see "Fill in the Blanks"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I click on "Disable" "link" in the "Accordion" "table_row"
    When I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "[data-action=Add-content]" "css_element"
    Then I should not see "Accordion"
    And I should see "Fill in the Blanks"
