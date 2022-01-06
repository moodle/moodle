@mod @mod_h5pactivity @core_h5p @_file_upload @_switch_iframe @current
Feature: Content bank link in the activity settings form
  In order to have direct access to the Content bank
  As a teacher
  I need to see a Content bank link in the activity settings

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |

  @javascript
  Scenario: The content bank link should go to the course Content bank
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    Then I should see "Use the content bank (opens in new window) to manage your H5P files"
    And I click on "content bank (opens in new window)" "link" in the "General" "fieldset"
    And I should see "C1" in the "page-navbar" "region"
    And I close all opened windows

  Scenario: Content bank is not displayed if the user don't have access to the content bank
    Given the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/contentbank:access  | Prevent    | editingteacher | Course       | C1        |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "H5P" to section "1"
    Then I should not see "Use the content Bank (opens in new window) to manage your H5P files"
