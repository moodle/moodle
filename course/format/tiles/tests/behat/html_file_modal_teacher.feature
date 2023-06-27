@format @format_tiles @format_tiles_mod_modal @format_tiles_html_modal_teacher @javascript @_file_upload
Feature: HTML file can be set to open in modal windows with subtiles off
  In order to improve UX
  As a user
  I need to be able to use these modals

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | coursedisplay | numsections | enablecompletion |
      | Course 1 | C1        | tiles  | 0             | 5           | 1                |
    And the following "activities" exist:
      | activity | name           | intro                 | course | idnumber | section | visible |
      | page     | Test page name | Test page description | C1     | page1    | 1       | 1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following config values are set as admin:
      | config                 | value    | plugin       |
      | enablecompletion       | 1        | core         |
      | modalmodules           | page     | format_tiles |
      | modalresources         | pdf,html | format_tiles |
      | assumedatastoreconsent | 1        | format_tiles |
      | reopenlastsection      | 0        | format_tiles |
      | usejavascriptnav       | 1        | format_tiles |

    When I log in as "teacher1"
    And format_tiles subtiles are off for course "Course 1"
    And I am on "Course 1" course homepage with editing mode on
    And I wait until the page is ready
    And I follow "Collapse all"
    And I wait until the page is ready
    And I expand section "1" for edit
    And I wait until the page is ready
    And I wait "3" seconds
    And I add a "File" to section "1"
    And I wait until the page is ready

    And I set the following fields to these values:
      | Name        | Test HTML file         |
      | Description | File description       |
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I upload "course/format/tiles/tests/fixtures/test.html" file to "Select files" filemanager
    And I expand all fieldsets
    And I set the field "Show type" to "1"
    And I press "Save and return to course"
    Then I should see "Test HTML file"
    And I wait "2" seconds
    And I am on "Course 1" course homepage
    And I log out tiles

  #  First check can see the HTML with subtiles off
  @javascript
  Scenario: Open section 1 view HTML file as teacher with subtiles off
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And format_tiles subtiles are off for course "Course 1"
    And I click on tile "1"
    And I wait until the page is ready
    And I click format tiles activity "Test HTML file"
    And I wait until the page is ready
    Then "Test HTML file" "dialogue" should be visible
#    TODO test that we can see "Test HTML file content" too (is in embedded HTML document virtual element)?
    And I click on "Click to toggle completion status" "button" in the "Test HTML file" "dialogue"
    And I click on "Click to toggle completion status" "button" in the "Test HTML file" "dialogue"
    And "Close" "button" should exist in the "Test HTML file" "dialogue"
    And I click on "Close" "button"
    And I wait until the page is ready
    And "Test HTML file" "dialogue" should not be visible
    And I click on close button for tile "1"
    And I log out tiles

#  Now with subtiles on
  @javascript
  Scenario: Open section 1 add HTML file as teacher with subtiles on
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And format_tiles subtiles are on for course "Course 1"
    And I click on tile "1"
    And I wait until the page is ready
    And I click format tiles activity "Test HTML file"
    And I wait until the page is ready
    Then "Test HTML file" "dialogue" should be visible
    #    TODO test that we can see "Test HTML file content" too (is in embedded HTML document virtual element)?
    And I click on "Click to toggle completion status" "button" in the "Test HTML file" "dialogue"
    And I click on "Click to toggle completion status" "button" in the "Test HTML file" "dialogue"
    And "Close" "button" should exist in the "Test HTML file" "dialogue"
    And I click on "Close" "button"
    And I wait until the page is ready
    And "Test HTML file" "dialogue" should not be visible
    And I click on close button for tile "1"
    And I log out tiles
