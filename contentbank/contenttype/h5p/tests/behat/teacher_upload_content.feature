@core @core_contentbank @core_h5p @contenttype_h5p @_file_upload @_switch_iframe @javascript
Feature: H5P file upload to content bank for non admins
  In order import new H5P content to content bank
  As an admin
  I need to be able to upload a new .h5p file to content bank

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | editingteacher |
    And I log in as "teacher1"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"

  Scenario: Teachers can not access system level content bank
    Given I press "Customise this page"
    And I add the "Navigation" block if not present
    When I expand "Site pages" node
    Then I should not see "Content bank"

  Scenario: Teachers can access course level content bank
    Given I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation" block if not present
    When I expand "Site pages" node
    Then I should see "Content bank"

  Scenario: Teachers can upload .h5p extension files to course content bank
    Given I log out
    And I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation" block if not present
    When I expand "Site pages" node
    And I click on "Content bank" "link"
    Then I should not see "filltheblanks.h5p"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "filltheblanks.h5p"

  Scenario: Other teachers can see uploaded H5P contents
    Given I log out
    And I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation" block if not present
    When I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "filltheblanks.h5p"
    And I log out
    When I log in as "teacher2"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    Then I should see "filltheblanks.h5p"

  Scenario: Teachers can not upload and deploy content types when libraries are not installed
    Given I log out
    And I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I should not see "Fill in the Blanks"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    When I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    Then I should see "Sorry, this file is not valid."
    And I should not see "filltheblanks.h5p"
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should not see "filltheblanks.h5p"

  Scenario: Teachers can not see existing contents when libraries are not installed
    Given I log out
    And I log in as "admin"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I should not see "Fill in the Blanks"
    When I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I wait until the page is ready
    And I should see "Fill in the Blanks"
    And I log out
    And I log in as "teacher1"
    Given I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation" block if not present
    When I expand "Site pages" node
    And I click on "Content bank" "link"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then I should see "Of which countries"
    Then I should not see "missing-required-library"
    And I switch to the main frame
    Given I log out
    And I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    When I click on "Delete version" "link" in the "Fill in the Blanks" "table_row"
    And I press "Continue"
    Then I should not see "Fill in the Blanks"
    And I log out
    And I log in as "teacher1"
    Given I am on "Course 1" course homepage
    When I expand "Site pages" node
    And I click on "Content bank" "link"
    Then I should not see "filltheblanks.h5p"
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should see "filltheblanks.h5p"
    And I click on "filltheblanks.h5p" "link"
    And I switch to "h5p-player" class iframe
    And I should see "missing-required-library"
