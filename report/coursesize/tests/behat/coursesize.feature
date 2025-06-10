@report @report_coursesize @_file_upload
Feature: Course size report calculates correct information

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
      | Course 2 | C2 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |
    And the following config values are set as admin:
      | config     | value | plugin            |
      | calcmethod | live  | report_coursesize |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "File" to section "1" using the activity chooser
    And I set the following fields to these values:
      | Name                      | Myfile     |
    And I upload "report/coursesize/tests/fixtures/COPYING.txt" file to "Select files" filemanager
    And I press "Save and return to course"
    And I log out

  @javascript
  Scenario: Check coursesize report for course 1
    When I log in as "admin"
    And I navigate to "Reports > Course size" in site administration
    Then I should see "File usage report"
    And I should see "34.3" in the "#coursesize_C1" "css_element"
    And I should not see "C2"
