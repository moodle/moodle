@editor @filter @filter_displayh5p @core_h5p @_file_upload @_switch_iframe
Feature: Render H5P content using filters
  To write rich text - I need to render H5P content.

  Background:
    Given the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And the "displayh5p" filter is "on"
    And the following config values are set as admin:
      | allowedsources | https://moodle.h5p.com/content/[id]/embed | filter_displayh5p |

  @javascript @external
  Scenario: Render an external H5P content URL.
    Given the following "activities" exist:
      | activity | name      | intro     | introformat | course | content                                                                      | contentformat | idnumber |
      | page     | PageName1 | PageDesc1 | 1           | C1     | <div>Go for it</div>https://moodle.h5p.com/content/1290772960722742119/embed | 1             | 1        |
    When I am on the PageName1 "page activity" page logged in as teacher1
    And I switch to "h5p-iframe" class iframe
    Then I should see "Lorum ipsum"

  @javascript
  Scenario: Add an external H5P content URL in a link with the URL. Should be rendered.
    Given the following "activities" exist:
      | activity | name      | intro     | introformat | course | content                                                                                                                         | contentformat | idnumber |
      | page     | PageName1 | PageDesc1 | 1           | C1     | <a href='https://moodle.h5p.com/content/1290772960722742119/embed'>https://moodle.h5p.com/content/1290772960722742119/embed</a> | 1             | 1        |
    When I am on the PageName1 "page activity" page logged in as teacher1
    And I switch to "h5p-iframe" class iframe
    Then I should see "Lorum ipsum"

  Scenario: Add an external H5P content URL in a link with text. Shouldn't be rendered.
#   This content won't be displayed, so this scenario shouldn't be labeled as external.
    Given the following "activities" exist:
      | activity | name      | intro     | introformat | course | content                                                                                         | contentformat | idnumber |
      | page     | PageName1 | PageDesc1 | 1           | C1     | <a href='https://moodle.h5p.com/content/1290772960722742119/embed'>Here you are the content</a> | 1             | 1        |
    When I am on the PageName1 "page activity" page logged in as teacher1
    Then ".h5p-iframe" "css_element" should not exist

  @javascript
  Scenario: Render a local H5P file as admin
    Given the following "activities" exist:
      | activity | name      | intro     | introformat | course | content                                                                          | contentformat | idnumber | [[files::content]]                        |
      | page     | PageName1 | PageDesc1 | 1           | C1     | <p>H5P Activity:</p><div class="h5p-placeholder">@@PLUGINFILE@@/ipsums.h5p</div> | 1             | 1        | h5p/tests/fixtures/ipsums.h5p::ipsums.h5p |
    When I am on the PageName1 "page activity" page logged in as teacher1
    And I should see "PageName1" in the "page-header" "region"
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page
    And I switch to "h5p-iframe" class iframe
    Then I should see "Lorum ipsum"
    And I switch to the main frame
    And I log out
    And I am on the PageName1 "page activity" page logged in as student1
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page
    And I switch to "h5p-iframe" class iframe
    Then I should not see "you don't have access"
    And I should see "Lorum ipsum"

  @javascript
  Scenario: Render a local H5P file as teacher
    Given the following "activities" exist:
      | activity | name      | intro     | introformat | course | content                                                                          | contentformat | idnumber | [[files::content]]                                  |
      | page     | PageName1 | PageDesc1 | 1           | C1     | <p>H5P Activity:</p><div class="h5p-placeholder">@@PLUGINFILE@@/ipsums.h5p</div> | 1             | 1        | h5p/tests/fixtures/ipsums.h5p::ipsums.h5p::teacher1 |
    When I am on the PageName1 "page activity" page logged in as teacher1
    And I should see "PageName1" in the "page-header" "region"
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
    Then I should see "Note that the libraries may exist in the file you uploaded, but you're not allowed to upload new libraries."
    And I should see "missing-required-library"

  @javascript
  Scenario: Render a local H5P file with existing libraries
    Given the following "activities" exist:
      | activity | name      | intro     | introformat | course | content                                                                          | contentformat | idnumber | [[files::content]]                                  |
      | page     | PageName1 | PageDesc1 | 1           | C1     | <p>H5P Activity:</p><div class="h5p-placeholder">@@PLUGINFILE@@/ipsums.h5p</div> | 1             | 1        | h5p/tests/fixtures/ipsums.h5p::ipsums.h5p::teacher1 |
      | page     | PageName2 | PageDesc2 | 1           | C1     | <p>H5P Activity:</p><div class="h5p-placeholder">@@PLUGINFILE@@/ipsums.h5p</div> | 1             | 1        | h5p/tests/fixtures/ipsums.h5p::ipsums.h5p::admin    |
    When I am on the PageName1 "page activity" page logged in as teacher1
    And I should see "PageName1" in the "page-header" "region"
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Libraries don't exist, so an error should be displayed.
    Then I should see "missing-required-library"
    And I switch to the main frame
    And I am on the PageName2 "page activity" page logged in as admin
    And I should see "PageName2" in the "page-header" "region"
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page
    And I switch to "h5p-iframe" class iframe
#   Libraries have been installed.
    Then I should see "Lorum ipsum"
    And I switch to the main frame
    And I am on the PageName1 "page activity" page logged in as teacher1
#   Switch to iframe created by filter
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page
    And I switch to "h5p-iframe" class iframe
    Then I should not see "missing-required-library"
    And I should see "Lorum ipsum"

  @javascript
  Scenario: Render local H5P file with a disabled main library
    Given the following "activities" exist:
      | activity | name      | intro     | introformat | course | content                                                                          | contentformat | idnumber | [[files::content]]                        |
      | page     | PageName1 | PageDesc1 | 1           | C1     | <p>H5P Activity:</p><div class="h5p-placeholder">@@PLUGINFILE@@/ipsums.h5p</div> | 1             | 1        | h5p/tests/fixtures/ipsums.h5p::ipsums.h5p |
    When I am logged in as "admin"
# Upload manually the H5P content-type library and disable it.
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I click on "Disable" "link" in the "Accordion" "table_row"
    And I am on the PageName1 "page activity" page logged in as admin
    And I should see "PageName1" in the "page-header" "region"
    And I switch to "h5p-iframe" class iframe
#   Library is disabled, so an error should be displayed.
    Then I should see "This file can't be displayed because its content type is disabled."
    And I should not see "Lorum ipsum"
    And I switch to the main frame
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I click on "Enable" "link" in the "Accordion" "table_row"
#   Content should be deployed now that main library is enabled.
    And I am on the PageName1 "page activity" page
#   Switch to iframe created by filter.
    And I switch to "h5p-iframe" class iframe
#   Switch to iframe created by embed.php page.
    And I switch to "h5p-iframe" class iframe
    Then I should see "Lorum ipsum"
    And I should not see "This file can't be displayed because its content type is disabled."
    And I switch to the main frame
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I click on "Disable" "link" in the "Accordion" "table_row"
#   Library is disabled again, so an error should be displayed.
    And I am on the PageName1 "page activity" page
    And I switch to "h5p-iframe" class iframe
    Then I should see "This file can't be displayed because its content type is disabled."
    And I should not see "Lorum ipsum"
    And I switch to the main frame
