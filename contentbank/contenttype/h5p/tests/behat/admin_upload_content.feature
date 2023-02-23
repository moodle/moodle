@core @core_contentbank @core_h5p @contenttype_h5p @_file_upload @_switch_iframe @javascript
Feature: H5P file upload to content bank for admins
  In order import new H5P content to content bank
  As an admin
  I need to be able to upload a new .h5p file to content bank

  Background:
    Given the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And I log in as "admin"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"

  Scenario: Admins can upload .h5p extension files to content bank
    Given I should not see "filltheblanks.h5p"
    When I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And I wait until the page is ready
    Then I should see "filltheblanks.h5p"

  Scenario: Admins can see uploaded H5P contents
    Given I should not see "filltheblanks.h5p"
    When I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then I should see "Of which countries"

  Scenario: Users can't see content managed by disabled plugins
    Given I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And I wait until the page is ready
    And I should see "filltheblanks.h5p"
    And I navigate to "Plugins > Content bank > Manage content types" in site administration
    And I click on "Disable" "icon" in the "H5P" "table_row"
    And I wait until the page is ready
    When I navigate to "Plugins > Content bank" in site administration
    Then I should not see "filltheblanks.h5p"

  Scenario: Contents in a context are not available from other contexts
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    When I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And I wait until the page is ready
    Then I should see "filltheblanks.h5p"
    And I am on "Course 1" course homepage
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should not see "filltheblanks.h5p"

  Scenario: Admins can upload and deployed content types when libraries are not installed
    Given I navigate to "H5P > Manage H5P content types" in site administration
    And I should not see "Fill in the Blanks"
    And I follow "Dashboard"
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    And I should not see "filltheblanks.h5p"
    When I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    Then I should see "Of which countries"
    And I switch to the main frame
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I should see "Fill in the Blanks"
